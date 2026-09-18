<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Registro de precio/venta -> tabla `tbl_venta`.
 *
 * En el esquema legacy el "precio vigente" es la fila activa
 * (CV='V', cestado=1) más reciente de cada producto. Al confirmar un
 * pedido insertamos una fila histórica con cestado=0 para no alterar
 * el precio de catálogo.
 */
class Sale extends Model
{
    protected $table = 'tbl_venta';

    public $timestamps = false;

    protected $primaryKey = 'idventa';

    public $guarded = [];

    protected $casts = [
        'nprecio' => 'float',
        'cestado' => 'integer',
        'idproducto' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('CV', 'V')->where('cestado', 1);
    }
}
