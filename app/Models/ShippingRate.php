<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Tarifas de envío -> tabla `tbl_prod_envio` (por ciudad + empresa + rango). */
class ShippingRate extends Model
{
    protected $table = 'tbl_prod_envio';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'idmunicipio' => 'integer',
        'idunidadmedida' => 'integer',
        'idcia' => 'integer',
        'precio' => 'float',
        'p1' => 'float',
        'p2' => 'float',
        'estado' => 'integer',
    ];
}