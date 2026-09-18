<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Usuarios de administración -> tabla `tbl_usuario`.
 * En el dump solo hay un admin: sUsuario='ACER', sPassword='LA' (texto
 * plano en el sistema legacy; se mantiene compatible y se sugiere cambiarla).
 */
class AdminUser extends Model
{
    protected $table = 'tbl_usuario';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'idciudad' => 'integer',
        'sEstado' => 'integer',
    ];

    /** Compara la contraseña admin (legacy: texto plano; se acepta bcrypt). */
    public function checkPassword(string $password): bool
    {
        if (str_starts_with($this->sPassword, '$2y$')) {
            return \Illuminate\Support\Facades\Hash::check($password, $this->sPassword);
        }

        return hash_equals(trim($this->sPassword), $password);
    }
}