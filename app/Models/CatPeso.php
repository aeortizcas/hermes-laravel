<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Categorías de peso -> tabla `tbl_catpeso`. */
class CatPeso extends Model
{
    protected $table = 'tbl_catpeso';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];
}