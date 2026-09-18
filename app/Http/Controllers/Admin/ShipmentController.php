<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tracking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Gestión de envíos en el panel (tbl_rastreo), equivalente a la ventana
 * de "Actividades" del DesktopApp legacy:
 *  - lista todas las filas de tbl_rastreo con usuario, producto, empresa,
 *    método de pago, ciudad, montos y fechas
 *  - el admin avanza el envío por etapas: fechapro (procesado) ->
 *    fechaencargo (entregado al courier) -> fechaentrega (entregado),
 *    y registra el código de seguimiento del courier (codigoenviocia).
 */
class ShipmentController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->query('status', 'all'); // all|pending|processing|shipped|delivered

        $query = Tracking::query()
            ->with(['product', 'company', 'paymentMethod', 'city', 'user'])
            ->orderByDesc('id');

        $query = match ($status) {
            'pending' => $query->where('estado', 1)->whereNull('fechapro'),
            'processing' => $query->whereNotNull('fechapro')->whereNull('fechaentrega'),
            'shipped' => $query->whereNotNull('fechapro')->whereNull('fechaentrega'),
            'delivered' => $query->whereNotNull('fechaentrega'),
            default => $query,
        };

        return Inertia::render('Admin/Shipments/Index', [
            'status' => $status,
            'counts' => [
                'pending' => Tracking::query()->where('estado', 1)->whereNull('fechapro')->count(),
                'processing' => Tracking::query()->whereNotNull('fechapro')->whereNull('fechaentrega')->count(),
                'delivered' => Tracking::query()->whereNotNull('fechaentrega')->count(),
            ],
            'shipments' => $query->get()->map(fn (Tracking $t) => $this->serialize($t))->values(),
        ]);
    }

    public function show(int $tracking): Response
    {
        $t = Tracking::query()->with(['product', 'company', 'paymentMethod', 'city', 'user'])->findOrFail($tracking);

        return Inertia::render('Admin/Shipments/Show', [
            'shipment' => $this->serialize($t),
        ]);
    }

    /** Avanza el envío a una etapa y registra el código del courier. */
    public function advance(Request $request, int $tracking): RedirectResponse
    {
        $t = Tracking::query()->findOrFail($tracking);

        $data = $request->validate([
            'action' => ['required', 'in:procesar,encargar,entregar'],
            'codigoenviocia' => ['nullable', 'string', 'max:255'],
        ]);

        $now = now()->format('Y-m-d H:i:s');

        match ($data['action']) {
            'procesar' => $t->update(['fechapro' => $now]),
            'encargar' => $t->update([
                'fechaencargo' => $now,
                'codigoenviocia' => $data['codigoenviocia'] ?: $t->codigoenviocia,
            ]),
            'entregar' => $t->update(['fechaentrega' => now()->toDateString()]),
        };

        return redirect()->back()->with('success', 'Envío #'.$t->id.' actualizado.');
    }

    /** @return array<string, mixed> */
    private function serialize(Tracking $t): array
    {
        $user = $t->user;

        return [
            'id' => (int) $t->id,
            'usuario' => $user?->name ?? '—',
            'cliente' => trim((string) ($user?->sPN ?? '').' '.($user?->sPA ?? '')),
            'producto' => $t->product?->sProducto ?? '—',
            'empresa' => $t->company?->scompany ?? '—',
            'metodoPago' => $t->paymentMethod?->pago ?? '—',
            'ciudad' => $t->city?->sCiudad ?? '—',
            'cantidad' => (int) $t->cantidad,
            'precio' => round((float) $t->precio, 2),
            'subtotal' => round((float) $t->subtotal, 2),
            'iva' => round((float) $t->iva, 2),
            'precioenvio' => round((float) $t->precioenvio, 2),
            'total' => round((float) $t->total, 2),
            'fechasol' => $t->fechasol,
            'fechapro' => $t->fechapro,
            'fechaencargo' => $t->fechaencargo,
            'fechaentrega' => $t->fechaentrega,
            'codigoenvio' => $t->codigoenvio,
            'codigoenviocia' => $t->codigoenviocia,
            'etapa' => $this->stage($t),
        ];
    }

    private function stage(Tracking $t): string
    {
        if ($t->fechaentrega !== null) {
            return 'delivered';
        }
        if ($t->fechaencargo !== null) {
            return 'shipped';
        }
        if ($t->fechapro !== null) {
            return 'processing';
        }

        return 'pending';
    }
}