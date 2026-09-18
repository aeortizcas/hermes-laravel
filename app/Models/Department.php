<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Departamentos -> tabla `tbl_departamento`. */
class Department extends Model
{
    protected $table = 'tbl_departamento';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'idpais' => 'integer',
        'sEstado' => 'integer',
    ];
}