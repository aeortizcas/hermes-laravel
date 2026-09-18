<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Support\AdminSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthController extends Controller
{
    public function showLogin(): Response|RedirectResponse
    {
        if (AdminSession::check()) {
            return redirect()->route('admin.dashboard');
        }

        return Inertia::render('Admin/Login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'usuario' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        $user = AdminUser::query()
            ->where('sUsuario', $data['usuario'])
            ->where('sEstado', '!=', 0)
            ->first();

        if (! $user || ! $user->checkPassword($data['password'])) {
            throw ValidationException::withMessages([
                'usuario' => 'Credenciales incorrectas.',
            ]);
        }

        AdminSession::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Bienvenido al panel, '.$user->sUsuario.'.');
    }

    public function logout(): RedirectResponse
    {
        AdminSession::logout();

        return redirect()->route('admin.login')->with('success', 'Sesión de administrador cerrada.');
    }
}