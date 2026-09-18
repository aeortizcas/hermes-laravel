<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Datos del receptor del envío -> tabla `tbl_corresponsal`. */
class Corresponsal extends Model
{
    protected $table = 'tbl_corresponsal';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'idrastro' => 'integer',
        'precioenvio' => 'float',
        'estado' => 'integer',
    ];
}