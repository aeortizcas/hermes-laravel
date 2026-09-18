<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Movimientos de inventario -> tabla `tbl_entrada`.
 * stipo: 'E' = entrada, 'S' = salida; nestado=1 = vigente.
 */
class InventoryEntry extends Model
{
    protected $table = 'tbl_entrada';

    public $timestamps = false;

    protected $primaryKey = 'identrada';

    public $guarded = [];

    protected $casts = [
        'ncantidad' => 'integer',
        'nestado' => 'integer',
    ];

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('nestado', 1);
    }

    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->where('stipo', $type);
    }

    /** Stock disponible: entradas (E) menos salidas (S) vigentes. */
    public static function available(int $productId): int
    {
        $in = static::query()
            ->where('idproducto', $productId)
            ->where('nestado', 1)
            ->where('stipo', 'E')
            ->sum('ncantidad');

        $out = static::query()
            ->where('idproducto', $productId)
            ->where('nestado', 1)
            ->where('stipo', 'S')
            ->sum('ncantidad');

        return max(0, (int) $in - (int) $out);
    }
}
