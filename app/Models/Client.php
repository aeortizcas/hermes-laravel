<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Clientes -> tabla `register`. */
class Client extends Model
{
    protected $table = 'register';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $hidden = ['password'];

    protected $casts = [
        'idciudad' => 'integer',
        'sEstado' => 'integer',
    ];
}