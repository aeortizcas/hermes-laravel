<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Clientes/usuarios legacy -> tabla `register`.
 */
class User extends Model
{
    protected $table = 'register';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $hidden = ['password'];

    protected $guarded = [];

    protected $casts = [
        'sEstado' => 'integer',
        'idciudad' => 'integer',
    ];
}
