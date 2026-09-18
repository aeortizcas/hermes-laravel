<?php

namespace App\Http\Middleware;

use App\Support\AdminSession;
use App\Support\AuthSession;
use App\Support\Cart;
use App\Support\Content;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /** @var string */
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
            'cartCount' => Cart::count(),
            'auth' => AuthSession::share(),
            'admin' => AdminSession::share(),
            'menu' => Content::menu(),
        ];
    }
}
