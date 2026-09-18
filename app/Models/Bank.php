<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Bancos -> tabla `tbl_banco`. */
class Bank extends Model
{
    protected $table = 'tbl_banco';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'idmoneda' => 'integer',
        'estado' => 'integer',
    ];
}