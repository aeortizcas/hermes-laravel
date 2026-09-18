<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Países -> tabla `tbl_pais`. */
class Country extends Model
{
    protected $table = 'tbl_pais';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'sEstado' => 'integer',
    ];
}