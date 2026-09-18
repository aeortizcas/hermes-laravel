<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Línea de pedido -> tabla `tbl_detalle`.
 * cestado=1 + Y=1 = línea activa.
 */
class OrderDetail extends Model
{
    protected $table = 'tbl_detalle';

    public $timestamps = false;

    protected $primaryKey = 'iddetalle';

    public $guarded = [];

    protected $casts = [
        'idmaestro' => 'integer',
        'idproducto' => 'integer',
        'ncantidad' => 'integer',
        'cestado' => 'integer',
        'Y' => 'integer',
        'dfecha' => 'datetime',
        'dfechaA' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'idproducto', 'id');
    }
}
