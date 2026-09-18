<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMaster;
use App\Models\Product;
use App\Models\Tracking;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'counts' => [
                'products' => Product::query()->count(),
                'categories' => \App\Models\ProductCategory::query()->count(),
                'customers' => User::query()->where('sEstado', '!=', 0)->count(),
                'orders' => OrderMaster::query()->count(),
                'shipments' => Tracking::query()->count(),
                'pendingShipments' => Tracking::query()
                    ->where('estado', 1)
                    ->whereNull('fechapro')
                    ->count(),
            ],
        ]);
    }
}