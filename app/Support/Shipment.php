<?php

namespace App\Support;

use App\Models\City;
use App\Models\Corresponsal;
use App\Models\InventoryEntry;
use App\Models\OrderDetail;
use App\Models\ProductWeight;
use App\Models\ShippingRate;
use App\Models\Tracking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Flujo de envío/pedido legacy sobre `tbl_rastreo`:
 *
 *  1. El cliente elige empresa de envío (tbl_tracking_emp) y método de
 *     pago (tbl_metodo_pago) por producto (legacy paso1.php) -> se crea
 *     una fila en tbl_rastreo (estado=1, fechasol=ahora).
 *  2. El cliente elige el municipio/ciudad destino (tbl_ciudad); se
 *     calculan peso (tbl_peso_p), IVA 15 % y costo de envío
 *     (tbl_prod_envio por rango p1..p2) (legacy seguimiento_envio1/paso2).
 *  3. Confirmación: se genera el codigoenvio, se guarda el corresponsal
 *     (tbl_corresponsal) y se inserta la línea en tbl_detalle (Y=0,
 *     igual que el legacy) (legacy seguimiento_envio_datos_confirmacion).
 */
class Shipment
{
    public const IVA_RATE = 0.15;

    /** Crea una fila de tbl_rastreo por línea del carrito. */
    public static function createFromCart(int $userId, array $shippingByProduct): Collection
    {
        $lines = Cart::lines();
        if ($lines->isEmpty()) {
            throw new \RuntimeException('El carrito está vacío.');
        }

        $created = collect();

        foreach ($lines as $line) {
            $product = $line['product'];
            $opts = $shippingByProduct[$product['id']] ?? null;

            if (! $opts || empty($opts['cia']) || empty($opts['pago'])) {
                throw new \RuntimeException('Falta empresa de envío o método de pago para '.$product['name'].'.');
            }

            $tracking = Tracking::query()->create([
                'idusuario' => $userId,
                'idproducto' => $product['id'],
                'idcia' => (int) $opts['cia'],
                'idmetodopago' => (int) $opts['pago'],
                'idmunicipio' => null,
                'cantidad' => $line['qty'],
                'precio' => $product['price'],
                'iva' => 0,
                'precioI' => 0,
                'preciot' => 0,
                'subtotal' => 0,
                'total' => 0,
                'precioenvio' => 0,
                'codigoenvio' => null,
                'fechasol' => now()->format('Y-m-d H:i:s'),
                'fechapro' => null,
                'fechaencargo' => null,
                'fechaentrega' => null,
                'estado' => 1,
            ]);

            $created->push($tracking);
        }

        return $created;
    }

    /**
     * Calcula la tarifa de envío para un pedido según empresa, ciudad y peso.
     *
     * @return array{precio: float, peso: float, ciudad: ?string}
     */
    public static function quote(int $trackingId, int $cityId): array
    {
        $tracking = Tracking::query()->findOrFail($trackingId);
        $weight = ProductWeight::query()
            ->where('idproducto', $tracking->idproducto)
            ->where('estado', '!=', 0)
            ->orderByDesc('id')
            ->value('pesop') ?? 0;

        $totalWeight = (float) $weight * (int) $tracking->cantidad;

        $rate = ShippingRate::query()
            ->where('idcia', $tracking->idcia)
            ->where('idmunicipio', $cityId)
            ->where('estado', '!=', 0)
            ->where('p1', '<=', $totalWeight)
            ->where('p2', '>=', $totalWeight)
            ->orderByDesc('id')
            ->first();

        $city = City::query()->find($cityId);

        return [
            'precio' => $rate ? (float) $rate->precio : 0.0,
            'peso' => $totalWeight,
            'ciudad' => $city?->sCiudad,
        ];
    }

    /** Fija municipio y montos (IVA, subtotal, total) en tbl_rastreo. */
    public static function chooseDestination(int $trackingId, int $cityId): Tracking
    {
        $tracking = Tracking::query()->findOrFail($trackingId);
        $quote = self::quote($trackingId, $cityId);

        $subtotal = round((float) $tracking->precio * (int) $tracking->cantidad, 2);
        $iva = round($subtotal * self::IVA_RATE, 2);
        $shipping = $quote['precio'];
        $total = round($subtotal + $iva + $shipping, 2);

        $tracking->update([
            'idmunicipio' => $cityId,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'preciot' => $subtotal,
            'total' => $total,
            'precioenvio' => $shipping,
        ]);

        return $tracking->fresh();
    }

    /**
     * Confirma el envío: codigoenvio, corresponsal y línea en tbl_detalle.
     *
     * @param  array<string, mixed>  $customer
     * @return array<string, mixed>
     */
    public static function confirm(int $trackingId, array $customer): array
    {
        $tracking = Tracking::query()->findOrFail($trackingId);

        if ($tracking->idmunicipio === null) {
            throw new \RuntimeException('Aún no se eligió el destino del envío.');
        }

        // Ya confirmado: no duplicar corresponsal ni línea de detalle.
        if ($tracking->codigoenvio !== null) {
            return self::summary($tracking);
        }

        $code = self::buildCode($tracking);

        try {
            DB::transaction(function () use ($tracking, $customer, $code) {
                // Validar y descontar stock (salida 'S' en tbl_entrada).
                $available = InventoryEntry::available((int) $tracking->idproducto);
                if ($available < (int) $tracking->cantidad) {
                    throw new \RuntimeException(
                        'No hay stock suficiente para '.($tracking->product?->sProducto ?? 'el producto')
                        .' (disponible: '.$available.').'
                    );
                }

                InventoryEntry::query()->create([
                    'idproducto' => $tracking->idproducto,
                    'ncantidad' => (int) $tracking->cantidad,
                    'nestado' => 1,
                    'stipo' => 'S',
                    'dfecha' => now()->toDateString(),
                ]);

                $tracking->update([
                    'codigoenvio' => $code,
                    'fechapro' => now()->format('Y-m-d H:i:s'),
                ]);

                Corresponsal::query()->create([
                    'nombre' => $customer['name'] ?? null,
                    'Direccion' => $customer['direccion'] ?? null,
                    'telefono' => $customer['telefono'] ?? null,
                    'email' => $customer['email'] ?? null,
                    'banco' => $customer['banco'] ?? null,
                    'numerocta' => $customer['numero'] ?? null,
                    'idrastro' => $tracking->id,
                    'codenvio' => $code,
                    'precioenvio' => $tracking->precioenvio,
                    'estado' => 1,
                ]);

                // Línea de detalle con Y=0 (igual que el legacy; Y=1 es "activa"
                // en el flujo de carrito por tabla).
                OrderDetail::query()->create([
                    'idproducto' => $tracking->idproducto,
                    'ncantidad' => $tracking->cantidad,
                    'cestado' => 1,
                    'Y' => 0,
                    'dfecha' => now()->toDateString(),
                ]);
            });
        } catch (Throwable $e) {
            throw $e;
        }

        return self::summary($tracking);
    }

    /** Resumen del envío (usado al confirmar y al re-confirmar). */
    private static function summary(Tracking $tracking): array
    {
        return [
            'id' => (int) $tracking->id,
            'codigoenvio' => $tracking->codigoenvio,
            'producto' => $tracking->product?->sProducto,
            'cantidad' => (int) $tracking->cantidad,
            'ciudad' => $tracking->city?->sCiudad,
            'empresa' => $tracking->company?->scompany,
            'metodoPago' => $tracking->paymentMethod?->pago,
            'subtotal' => round((float) $tracking->subtotal, 2),
            'iva' => round((float) $tracking->iva, 2),
            'precioenvio' => round((float) $tracking->precioenvio, 2),
            'total' => round((float) $tracking->total, 2),
        ];
    }

    /** Código único de envío (mismo patrón del legacy: id+ciudad+usuario+fecha). */
    private static function buildCode(Tracking $tracking): string
    {
        $cityPrefix = $tracking->city
            ? mb_substr(trim((string) $tracking->city->sCiudad), 0, 2)
            : 'XX';
        $userPrefix = $tracking->user
            ? mb_substr(trim((string) $tracking->user->name), 0, 2)
            : 'XX';

        return $tracking->id.$cityPrefix.$userPrefix.now()->format('YmdHis');
    }

    /** Envíos de un usuario con relaciones cargadas. */
    public static function forUser(int $userId): Collection
    {
        return Tracking::query()
            ->with(['product', 'company', 'paymentMethod', 'city', 'corresponsal'])
            ->where('idusuario', $userId)
            ->where('estado', '!=', 0)
            ->orderByDesc('id')
            ->get();
    }

    /** Serializa una fila de tbl_rastreo para la UI. */
    public static function serialize(Tracking $t): array
    {
        return [
            'id' => (int) $t->id,
            'producto' => $t->product?->sProducto ?? '—',
            'cantidad' => (int) $t->cantidad,
            'precio' => round((float) $t->precio, 2),
            'subtotal' => round((float) $t->subtotal, 2),
            'iva' => round((float) $t->iva, 2),
            'precioenvio' => round((float) $t->precioenvio, 2),
            'total' => round((float) $t->total, 2),
            'empresa' => $t->company?->scompany ?? '—',
            'metodoPago' => $t->paymentMethod?->pago ?? '—',
            'ciudad' => $t->city?->sCiudad ?? null,
            'ciudadId' => $t->idmunicipio !== null ? (int) $t->idmunicipio : null,
            'codigoenvio' => $t->codigoenvio,
            'codigoenviocia' => $t->codigoenviocia,
            'fechasol' => $t->fechasol,
            'fechapro' => $t->fechapro,
            'fechaencargo' => $t->fechaencargo,
            'fechaentrega' => $t->fechaentrega,
            'estado' => (int) $t->estado,
            'pendienteDestino' => $t->idmunicipio === null,
            'corresponsal' => $t->relationLoaded('corresponsal') && $t->corresponsal ? [
                'nombre' => (string) $t->corresponsal->nombre,
                'direccion' => (string) ($t->corresponsal->Direccion ?? ''),
                'telefono' => (string) ($t->corresponsal->telefono ?? ''),
                'email' => (string) ($t->corresponsal->email ?? ''),
            ] : null,
        ];
    }
}