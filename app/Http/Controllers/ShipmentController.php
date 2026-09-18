<?php

namespace App\Http\Controllers;

use App\Support\AuthSession;
use App\Support\Cart;
use App\Support\Lookup;
use App\Support\Shipment;
use App\Support\Subscriptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Flujo de compra con envío (tbl_rastreo), equivalente al legacy
 * procesar1.php -> paso1.php -> seguimiento_envio1/3 -> confirmación.
 */
class ShipmentController extends Controller
{
    public function checkout(): Response|RedirectResponse
    {
        if (Cart::lines()->isEmpty()) {
            return redirect()->route('cart.index');
        }

        if (! AuthSession::check()) {
            return redirect()->route('login')->with('success', 'Inicia sesión para continuar con tu pedido.');
        }

        // Verificador de membresía (igual que el legacy verificador.php):
        // sin membresía activa se redirige a la página de suscripciones.
        if (! Subscriptions::hasActive(AuthSession::user())) {
            return redirect()->route('subscriptions.index')->with('success', 'Necesitas una membresía activa para hacer pedidos.');
        }

        return Inertia::render('Shipment/Checkout', [
            'lines' => Cart::lines(),
            'totals' => Cart::totals(),
            'companies' => Lookup::shippingCompanies(),
            'methods' => Lookup::paymentMethods(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = AuthSession::user();
        if (! $user) {
            return redirect()->route('login');
        }
        if (Cart::lines()->isEmpty()) {
            return redirect()->route('cart.index');
        }
        if (! Subscriptions::hasActive($user)) {
            return redirect()->route('subscriptions.index')->with('success', 'Necesitas una membresía activa para hacer pedidos.');
        }

        $data = $request->validate([
            'shipping' => ['required', 'array'],
            'shipping.*.cia' => ['required', 'integer', 'exists:tbl_tracking_emp,id'],
            'shipping.*.pago' => ['required', 'integer', 'exists:tbl_metodo_pago,id'],
        ]);

        try {
            $created = Shipment::createFromCart((int) $user->id, $data['shipping']);
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages(['shipping' => $e->getMessage()]);
        }

        Cart::clear();

        $first = $created->first();

        return redirect()
            ->route('shipments.index')
            ->with('success', 'Pedido registrado. Completa el destino de cada envío.');
    }

    public function index(): Response
    {
        $user = AuthSession::user();

        return Inertia::render('Shipment/Index', [
            'shipments' => $user
                ? Shipment::forUser((int) $user->id)->map(fn ($t) => Shipment::serialize($t))->values()
                : [],
            'cities' => Lookup::cities(),
        ]);
    }

    public function destination(Request $request, int $tracking): RedirectResponse
    {
        $user = AuthSession::user();
        $trackingModel = \App\Models\Tracking::query()->findOrFail($tracking);

        if (! $user || (int) $trackingModel->idusuario !== (int) $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'city' => ['required', 'integer', 'exists:tbl_ciudad,id'],
        ]);

        Shipment::chooseDestination($tracking, (int) $data['city']);

        return redirect()->back()->with('success', 'Destino actualizado.');
    }

    public function confirmPage(int $tracking): Response|RedirectResponse
    {
        $user = AuthSession::user();
        $model = \App\Models\Tracking::query()->findOrFail($tracking);

        if (! $user || (int) $model->idusuario !== (int) $user->id) {
            abort(403);
        }

        if ($model->idmunicipio === null) {
            return redirect()->route('shipments.index')->with('success', 'Primero elige el destino del envío.');
        }

        return Inertia::render('Shipment/Confirm', [
            'shipment' => Shipment::serialize($model),
            'banks' => Lookup::banks(),
        ]);
    }

    public function confirm(Request $request, int $tracking): RedirectResponse
    {
        $user = AuthSession::user();
        $model = \App\Models\Tracking::query()->findOrFail($tracking);

        if (! $user || (int) $model->idusuario !== (int) $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'direccion' => ['required', 'string', 'max:150'],
            'telefono' => ['required', 'string', 'max:45'],
            'email' => ['required', 'email', 'max:150'],
            'banco' => ['nullable', 'integer'],
            'numero' => ['nullable', 'string', 'max:45'],
        ]);

        try {
            $result = Shipment::confirm($tracking, $data);
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages(['name' => $e->getMessage()]);
        }

        return redirect()
            ->route('shipments.show', $tracking)
            ->with('success', 'Envío confirmado con código '.$result['codigoenvio'].'.');
    }

    public function show(int $tracking): Response
    {
        $user = AuthSession::user();
        $model = \App\Models\Tracking::query()->findOrFail($tracking);

        if (! $user || (int) $model->idusuario !== (int) $user->id) {
            abort(403);
        }

        return Inertia::render('Shipment/Show', [
            'shipment' => Shipment::serialize($model),
            'corresponsal' => $model->corresponsal,
        ]);
    }
}