<?php

namespace App\Http\Controllers;

use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Cart', [
            'lines' => Cart::lines(),
            'totals' => Cart::totals(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'qty' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        Cart::add((int) $data['product_id'], (int) ($data['qty'] ?? 1));

        return back()->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $data = $request->validate([
            'qty' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        Cart::set($product, (int) $data['qty']);

        return back();
    }

    public function destroy(int $product): RedirectResponse
    {
        Cart::remove($product);

        return back();
    }
}
