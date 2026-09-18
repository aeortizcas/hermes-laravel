<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Cabecera de pedido/carrito -> tabla `tbl_master`.
 * cestado=1 + sbi=0 = pedido abierto (convención legacy).
 */
class OrderMaster extends Model
{
    protected $table = 'tbl_master';

    public $timestamps = false;

    protected $primaryKey = 'idmaster';

    public $guarded = [];

    protected $casts = [
        'dfecha' => 'date:Y-m-d',
        'idusuario' => 'integer',
        'cestado' => 'integer',
        'sbi' => 'integer',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'idmaestro', 'idmaster');
    }
}
