<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Imágenes de producto -> tabla `tbl_producto_imagenes`.
 * Los archivos se sirven desde public/uploads/products.
 */
class ProductImage extends Model
{
    protected $table = 'tbl_producto_imagenes';

    public $timestamps = false;

    protected $primaryKey = 'id';

    public $guarded = [];

    protected $casts = [
        'idproducto' => 'integer',
        'portada' => 'integer',
        'cEstado' => 'integer',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        return asset('uploads/products/'.rawurlencode($this->images));
    }
}
