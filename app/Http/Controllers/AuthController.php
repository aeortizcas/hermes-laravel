<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\AuthSession;
use App\Support\LegacyPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register', [
            'cities' => \App\Support\Lookup::cities(),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'usuario' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()
            ->where('sEstado', '!=', 0)
            ->where(function ($q) use ($data) {
                $q->where('name', $data['usuario'])
                    ->orWhere('email', $data['usuario']);
            })
            ->first();

        if (! $user || ! LegacyPassword::verify($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'usuario' => 'Credenciales incorrectas.',
            ]);
        }

        AuthSession::login($user);

        return redirect()->intended(route('home'))->with('success', '¡Bienvenido de nuevo, '.$user->name.'!');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ciudad' => ['nullable', 'integer'],
            'usuario' => ['required', 'string', 'max:150', 'unique:register,name'],
            'email' => ['required', 'email', 'max:150', 'unique:register,email'],
            'primerNombre' => ['required', 'string', 'max:20'],
            'segundoNombre' => ['nullable', 'string', 'max:20'],
            'primerApellido' => ['required', 'string', 'max:20'],
            'segundoApellido' => ['nullable', 'string', 'max:20'],
            'telefono' => ['required', 'string', 'max:45'],
            'direccion' => ['required', 'string', 'max:45'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // Nuevos registros: bcrypt (compatible con password_verify del legacy).
        $user = User::query()->create([
            'idciudad' => $data['ciudad'] ?? null,
            'name' => $data['usuario'],
            'email' => mb_strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'sPN' => $data['primerNombre'],
            'sSN' => $data['segundoNombre'] ?? null,
            'sPA' => $data['primerApellido'],
            'sSA' => $data['segundoApellido'] ?? null,
            'sTelefono' => $data['telefono'],
            'sDireccion' => $data['direccion'],
            'sTipo' => 'I',
            'sEstado' => 1,
        ]);

        AuthSession::login($user);

        return redirect()->route('home')->with('success', '¡Cuenta creada! Bienvenido, '.$user->name.'.');
    }

    public function logout(): RedirectResponse
    {
        AuthSession::logout();

        return redirect()->route('home')->with('success', 'Sesión cerrada.');
    }
}