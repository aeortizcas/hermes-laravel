<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Peso de producto -> tabla `tbl_peso_p`. */
class ProductWeight extends Model
{
    protected $table = 'tbl_peso_p';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'idproducto' => 'integer',
        'pesop' => 'float',
        'estado' => 'integer',
    ];
}