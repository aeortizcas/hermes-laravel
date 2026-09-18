<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Catálogo de productos -> tabla `tbl_producto`.
 *
 * El precio vive en `tbl_venta` (sale) y el stock se calcula con
 * `tbl_entrada`; ver App\Support\Catalog.
 */
class Product extends Model
{
    protected $table = 'tbl_producto';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'estado' => 'integer',
        'idec' => 'integer',
        'idproveedor' => 'integer',
    ];

    /** Productos visibles en tienda. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('estado', '!=', 0);
    }

    /** Imágenes (activas) del producto. */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'idproducto', 'id')
            ->where('cEstado', '!=', 0)
            ->orderByDesc('portada')
            ->orderBy('id');
    }

    public function coverImage(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'idproducto', 'id')
            ->where('portada', 1)
            ->where('cEstado', '!=', 0)
            ->limit(1);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'idec', 'id');
    }
}
