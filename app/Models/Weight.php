<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Pesos de envío por unidad y empresa -> tabla `tbl_peso`. */
class Weight extends Model
{
    protected $table = 'tbl_peso';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'idunidadmedida' => 'integer',
        'npeso' => 'float',
        'estado' => 'integer',
        'idtracking_emp' => 'integer',
        'ncoste' => 'float',
    ];
}