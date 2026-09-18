<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\ShipmentController as AdminShipmentController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.attempt');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');            Route::prefix('productos')->name('products.')->group(function () {
                Route::get('/', [ProductController::class, 'index'])->name('index');
                Route::get('/nuevo', [ProductController::class, 'create'])->name('create');
                Route::post('/', [ProductController::class, 'store'])->name('store');
                Route::get('/{product}/editar', [ProductController::class, 'edit'])->name('edit');
                Route::put('/{product}', [ProductController::class, 'update'])->name('update');
                Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
            });

            Route::prefix('catalogos')->name('catalog.')->group(function () {
                Route::get('/{entity}', [CatalogController::class, 'index'])->name('index');
                Route::post('/{entity}', [CatalogController::class, 'store'])->name('store');
                Route::put('/{entity}/{id}', [CatalogController::class, 'update'])->name('update');
                Route::delete('/{entity}/{id}', [CatalogController::class, 'toggle'])->name('toggle');
            });

            Route::prefix('envios')->name('shipments.')->group(function () {
                Route::get('/', [AdminShipmentController::class, 'index'])->name('index');
                Route::get('/{tracking}', [AdminShipmentController::class, 'show'])->name('show');
                Route::post('/{tracking}/avanzar', [AdminShipmentController::class, 'advance'])->name('advance');
                Route::get('/{tracking}/factura', [InvoiceController::class, 'show'])->name('invoice');
            });

            Route::prefix('paginas')->name('pages.')->group(function () {
                Route::get('/', [PagesController::class, 'pages'])->name('index');
                Route::post('/', [PagesController::class, 'storePage'])->name('store');
                Route::put('/{id}', [PagesController::class, 'updatePage'])->name('update');
                Route::delete('/{id}', [PagesController::class, 'destroyPage'])->name('destroy');

                Route::get('/niveles', [PagesController::class, 'niveles'])->name('niveles');
                Route::post('/niveles', [PagesController::class, 'storeNivel'])->name('niveles.store');
                Route::delete('/niveles/{id}', [PagesController::class, 'destroyNivel'])->name('niveles.destroy');
            });
    });
});