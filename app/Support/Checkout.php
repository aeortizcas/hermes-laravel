<?php

namespace App\Support;

use App\Models\InventoryEntry;
use App\Models\OrderDetail;
use App\Models\OrderMaster;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Persiste un pedido en el esquema legacy con las mismas convenciones:
 *  - tbl_master  : cestado=1, sbi=0 (pedido abierto)
 *  - tbl_detalle : cestado=1, Y=1   (línea activa)
 *  - tbl_venta   : fila histórica de precio con cestado=0 (no altera el
 *                  precio vigente de catálogo, que usa cestado=1)
 *  - register    : el cliente se busca/crea por email (legacy: usuario)
 */
class Checkout
{
    /**
     * @param  array<string, mixed>  $customer  nombre, email, telefono...
     * @return array<string, mixed>  resumen del pedido creado
     */
    public static function placeOrder(array $customer): array
    {
        $lines = Cart::lines();
        if ($lines->isEmpty()) {
            throw new \RuntimeException('El carrito está vacío.');
        }

        $user = self::syncCustomer($customer);

        try {
            $order = DB::transaction(function () use ($user, $lines) {
                $master = OrderMaster::query()->create([
                    'dfecha' => now()->toDateString(),
                    'idusuario' => $user?->id,
                    'cestado' => 1,
                    'sbi' => 0,
                ]);

                foreach ($lines as $line) {
                    $product = $line['product'];
                    $price = (float) $product['price'];
                    $qty = (int) $line['qty'];

                    // Validar y descontar stock (salida 'S' en tbl_entrada).
                    $available = InventoryEntry::available((int) $product['id']);
                    if ($available < $qty) {
                        throw new \RuntimeException(
                            'No hay stock suficiente para '.$product['name']
                            .' (disponible: '.$available.').'
                        );
                    }

                    InventoryEntry::query()->create([
                        'idproducto' => $product['id'],
                        'ncantidad' => $qty,
                        'nestado' => 1,
                        'stipo' => 'S',
                        'dfecha' => now()->toDateString(),
                    ]);

                    OrderDetail::query()->create([
                        'idmaestro' => $master->idmaster,
                        'idproducto' => $product['id'],
                        'ncantidad' => $line['qty'],
                        'cestado' => 1,
                        'Y' => 1,
                    ]);

                    // Snapshot del precio (histórico; no afecta catálogo).
                    Sale::query()->create([
                        'nprecio' => $price,
                        'dfecha' => now()->toDateString(),
                        'CV' => 'V',
                        'cestado' => 0,
                        'idproducto' => $product['id'],
                    ]);
                }

                return $master;
            });
        } catch (Throwable $e) {
            Log::error('Checkout fallido: '.$e->getMessage());

            throw $e;
        }

        $totals = Cart::totals();

        return [
            'id' => (int) $order->idmaster,
            'fecha' => $order->dfecha,
            'items' => $lines->map(fn ($line) => [
                'name' => $line['product']['name'],
                'qty' => $line['qty'],
                'price' => $line['product']['price'],
                'subtotal' => $line['subtotal'],
            ])->values()->all(),
            'totals' => $totals,
            'customer' => $customer,
        ];
    }

    /** @return User|null */
    private static function syncCustomer(array $customer): ?User
    {
        $email = mb_strtolower(trim($customer['email'] ?? ''));
        if ($email === '') {
            return null;
        }

        $user = User::query()->where('email', $email)->where('sEstado', '!=', 0)->first();
        if ($user) {
            return $user;
        }

        $name = trim($customer['name'] ?? '');
        $handle = $name !== ''
            ? Str::slug($name, '_').'_'.Str::lower(Str::random(4))
            : 'invitado_'.Str::lower(Str::random(8));

        return User::query()->create([
            'name' => $handle,
            'email' => $email,
            'password' => Hash::make(Str::random(24)),
            'sPN' => $name !== '' ? $name : null,
            'sTelefono' => $customer['telefono'] ?? null,
            'sDireccion' => $customer['direccion'] ?? null,
            'sTipo' => 'I',
            'sEstado' => 1,
        ]);
    }
}
