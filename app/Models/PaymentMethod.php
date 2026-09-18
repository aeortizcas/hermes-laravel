<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Métodos de pago -> tabla `tbl_metodo_pago`. */
class PaymentMethod extends Model
{
    protected $table = 'tbl_metodo_pago';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];
}