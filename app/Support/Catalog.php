<?php

namespace App\Support;

use App\Models\InventoryEntry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Sale;
use Illuminate\Support\Collection;

/**
 * Consultas de catálogo sobre el esquema legacy.
 *
 * El precio "vigente" de un producto es la fila activa más reciente de
 * tbl_venta (CV='V', cestado=1); el stock disponible se calcula como
 * entradas (stipo='E') menos salidas (stipo='S') de tbl_entrada con
 * nestado=1. Se replican esas reglas con subconsultas limpias (el código
 * legacy las hacía con joins que multiplicaban filas).
 */
class Catalog
{
    /** IVA usado por el carrito legacy (15 %). */
    public const IVA_RATE = 0.15;

    /**
     * Productos visibles, opcionalmente filtrados por categoría/búsqueda.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public static function products(?int $categoryId = null, ?string $search = null): Collection
    {
        $query = self::baseQuery();

        if ($categoryId !== null) {
            $query->where('idec', $categoryId);
        }

        if ($search !== null && trim($search) !== '') {
            $query->where('sProducto', 'like', '%'.$search.'%');
        }

        return $query->get()
            ->map(fn (Product $p) => self::serialize($p))
            ->filter(fn (array $row) => $row['price'] !== null && $row['cover'] !== null)
            ->values();
    }

    /**
     * Hidrata varios productos (p. ej. para el carrito): id => fila.
     *
     * @param  int[]  $ids
     * @return Collection<int, array<string, mixed>>
     */
    public static function forProducts(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return self::baseQuery()
            ->whereIn('tbl_producto.id', $ids)
            ->get()
            ->map(fn (Product $p) => self::serialize($p))
            ->keyBy('id');
    }

    /**
     * Detalle de un producto (o null si no existe / no está visible).
     *
     * @return array<string, mixed>|null
     */
    public static function productById(int $id): ?array
    {
        $product = self::baseQuery()->whereKey($id)->first();

        return $product ? self::serialize($product) : null;
    }

    /** Builder base con precio vigente + entradas/salidas de inventario. */
    private static function baseQuery()
    {
        return Product::query()
            ->visible()
            ->with(['category', 'coverImage'])
            ->addSelect([
                'current_price' => Sale::query()
                    ->select('nprecio')
                    ->whereColumn('tbl_venta.idproducto', 'tbl_producto.id')
                    ->where('CV', 'V')
                    ->where('cestado', 1)
                    ->orderByDesc('idventa')
                    ->limit(1),
                'stock_in' => InventoryEntry::query()
                    ->selectRaw('COALESCE(SUM(ncantidad), 0)')
                    ->whereColumn('tbl_entrada.idproducto', 'tbl_producto.id')
                    ->where('nestado', 1)
                    ->where('stipo', 'E'),
                'stock_out' => InventoryEntry::query()
                    ->selectRaw('COALESCE(SUM(ncantidad), 0)')
                    ->whereColumn('tbl_entrada.idproducto', 'tbl_producto.id')
                    ->where('nestado', 1)
                    ->where('stipo', 'S'),
            ]);
    }

    /** Categorías de producto activas. */
    public static function categories(): Collection
    {
        return ProductCategory::query()
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn (ProductCategory $c) => [
                'id' => (int) $c->id,
                'name' => $c->name,
            ])
            ->values();
    }

    /** @return array<string, mixed> */
    private static function serialize(Product $p): array
    {
        $cover = $p->coverImage->first() ?? $p->images->first();

        $stockIn = (int) $p->stock_in;
        $stockOut = (int) $p->stock_out;
        $stock = $stockIn - $stockOut;

        return [
            'id' => (int) $p->id,
            'name' => trim((string) $p->sProducto),
            'description' => trim((string) $p->sDescripcion),
            'unit' => (string) $p->unidad,
            'price' => $p->current_price !== null ? round((float) $p->current_price, 2) : null,
            'stock' => max(0, $stock),
            'available' => $stock > 0,
            'category' => $p->category ? [
                'id' => (int) $p->category->id,
                'name' => (string) $p->category->name,
            ] : null,
            'cover' => $cover ? $cover->url : null,
            'images' => $p->relationLoaded('images')
                ? $p->images->map(fn ($img) => $img->url)->values()->all()
                : [],
        ];
    }
}
