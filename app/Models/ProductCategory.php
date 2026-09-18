<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Categorías de producto -> tabla `tbl_categories`.
 * (La tabla `categories` era de menú legacy y no se usa aquí.)
 */
class ProductCategory extends Model
{
    protected $table = 'tbl_categories';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $guarded = [];

    protected $casts = [
        'estado' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('estado', '!=', 0);
    }
}
