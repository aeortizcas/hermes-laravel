<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Session;

/**
 * Sesión de cliente sobre la tabla legacy `register`.
 * El legacy guardaba el login como $_SESSION['name']; aquí guardamos el id
 * y compartimos un objeto "auth" con Inertia (ver HandleInertiaRequests).
 */
class AuthSession
{
    private const KEY = 'hermes.auth.user_id';

    public static function login(User $user): void
    {
        Session::put(self::KEY, $user->id);
        Session::regenerate();
    }

    public static function logout(): void
    {
        Session::forget(self::KEY);
    }

    public static function user(): ?User
    {
        $id = Session::get(self::KEY);
        if (! $id) {
            return null;
        }

        return User::query()->whereKey($id)->where('sEstado', '!=', 0)->first();
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    /** Datos compartidos con Inertia (nunca la contraseña). */
    public static function share(): ?array
    {
        $user = self::user();
        if (! $user) {
            return null;
        }

        return [
            'id' => (int) $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'primerNombre' => (string) $user->sPN,
            'segundoNombre' => (string) $user->sSN,
            'primerApellido' => (string) $user->sPA,
            'segundoApellido' => (string) $user->sSA,
            'telefono' => (string) $user->sTelefono,
            'direccion' => (string) $user->sDireccion,
            'ciudad' => (int) ($user->idciudad ?? 0),
        ];
    }
}