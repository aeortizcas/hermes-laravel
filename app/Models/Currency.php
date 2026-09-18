<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Monedas -> tabla `tbl_moneda`. */
class Currency extends Model
{
    protected $table = 'tbl_moneda';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];
}