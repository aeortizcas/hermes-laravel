<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Proveedores -> tabla `tbl_proveedores`. */
class Provider extends Model
{
    protected $table = 'tbl_proveedores';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'idpais' => 'integer',
        'sEstado' => 'integer',
    ];
}