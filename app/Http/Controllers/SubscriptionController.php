<?php

namespace App\Http\Controllers;

use App\Support\AuthSession;
use App\Support\Subscriptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Página de membresías del cliente (legacy suscriptions/index.php + subscriptions.php). */
class SubscriptionController extends Controller
{
    public function index(): Response
    {
        $user = AuthSession::user();

        return Inertia::render('Subscription/Index', [
            'current' => Subscriptions::activeFor($user),
        ]);
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $user = AuthSession::user();
        if (! $user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para suscribirte.');
        }

        $data = $request->validate([
            'membership' => ['required', 'in:personal,business'],
        ]);

        $sub = Subscriptions::subscribe($user, $data['membership']);

        return redirect()->route('subscriptions.index')->with(
            'success',
            '¡Tu membresía '.($sub->membership_type === 'personal' ? 'Personal' : 'Empresarial').' está activa!'
        );
    }
}