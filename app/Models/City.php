<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Ciudades -> tabla `tbl_ciudad`. */
class City extends Model
{
    protected $table = 'tbl_ciudad';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'iddepartament' => 'integer',
        'sEstado' => 'integer',
    ];
}