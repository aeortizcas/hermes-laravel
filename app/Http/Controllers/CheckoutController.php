<?php

namespace App\Http\Controllers;

use App\Support\Cart;
use App\Support\Checkout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function index(): Response|RedirectResponse
    {
        if (Cart::lines()->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return Inertia::render('Checkout', [
            'lines' => Cart::lines(),
            'totals' => Cart::totals(),
        ]);
    }

    public function store(Request $request): Response|RedirectResponse
    {
        if (Cart::lines()->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:45'],
            'direccion' => ['nullable', 'string', 'max:45'],
        ]);

        try {
            $order = Checkout::placeOrder($data);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        Cart::clear();

        return Inertia::render('Checkout/Complete', ['order' => $order]);
    }
}
