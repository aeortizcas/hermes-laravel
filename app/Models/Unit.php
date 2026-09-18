<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Unidades de medida -> tabla `tbl_unidad_medidad`. */
class Unit extends Model
{
    protected $table = 'tbl_unidad_medidad';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];
}