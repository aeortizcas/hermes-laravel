<?php

namespace App\Support;

use App\Models\AdminUser;
use Illuminate\Support\Facades\Session;

/** Sesión del panel de administración (tbl_usuario). */
class AdminSession
{
    private const KEY = 'hermes.admin.user_id';

    public static function login(AdminUser $user): void
    {
        Session::put(self::KEY, $user->id);
        Session::regenerate();
    }

    public static function logout(): void
    {
        Session::forget(self::KEY);
    }

    public static function user(): ?AdminUser
    {
        $id = Session::get(self::KEY);
        if (! $id) {
            return null;
        }

        return AdminUser::query()->whereKey($id)->where('sEstado', '!=', 0)->first();
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function share(): ?array
    {
        $user = self::user();
        if (! $user) {
            return null;
        }

        return [
            'id' => (int) $user->id,
            'usuario' => (string) $user->sUsuario,
            'nombre' => trim((string) $user->sPrimerN.' '.$user->sApellidoN),
        ];
    }
}