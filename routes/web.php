<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categorias/{category}', [CategoryController::class, 'show'])->name('categoria.show');
Route::get('/producto/{product}', [ProductController::class, 'show'])->name('producto.show');

Route::prefix('carrito')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/agregar', [CartController::class, 'store'])->name('add');
    Route::post('/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/{product}', [CartController::class, 'destroy'])->name('remove');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Autenticación de clientes (tabla legacy `register`)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Membresías / suscripciones
Route::get('/suscripcion', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::post('/suscripcion', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');

// Pedidos con envío (tbl_rastreo)
Route::get('/pedido', [ShipmentController::class, 'checkout'])->name('shipment.checkout');
Route::post('/pedido', [ShipmentController::class, 'store'])->name('shipment.store');
Route::get('/mis-envios', [ShipmentController::class, 'index'])->name('shipments.index');
Route::get('/mis-envios/{tracking}', [ShipmentController::class, 'show'])->name('shipments.show');
Route::get('/mis-envios/{tracking}/factura', [InvoiceController::class, 'show'])->name('shipments.invoice');
Route::post('/mis-envios/{tracking}/destino', [ShipmentController::class, 'destination'])->name('shipments.destination');
Route::get('/mis-envios/{tracking}/confirmar', [ShipmentController::class, 'confirmPage'])->name('shipments.confirm');
Route::post('/mis-envios/{tracking}/confirmar', [ShipmentController::class, 'confirm'])->name('shipments.confirm.store');