<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Empresas de envío -> tabla `tbl_tracking_emp`. */
class TrackingCompany extends Model
{
    protected $table = 'tbl_tracking_emp';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];
}