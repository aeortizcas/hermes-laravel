<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Carrito por sesión (guest). No toca las tablas legacy: al confirmar la
 * compra se persiste en tbl_master/tbl_detalle (ver Checkout).
 */
class Cart
{
    private const KEY = 'hermes.cart'; // ['productId' => qty]

    public static function add(int $productId, int $qty = 1): void
    {
        $items = self::items();
        $items[$productId] = max(1, ($items[$productId] ?? 0) + max(1, $qty));
        self::save($items);
    }

    public static function set(int $productId, int $qty): void
    {
        $items = self::items();
        if ($qty <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = $qty;
        }
        self::save($items);
    }

    public static function remove(int $productId): void
    {
        $items = self::items();
        unset($items[$productId]);
        self::save($items);
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
    }

    /** @return array<int, int> productId => qty */
    public static function items(): array
    {
        $items = Session::get(self::KEY, []);

        return is_array($items)
            ? array_filter(array_map('intval', $items), fn (int $q) => $q > 0)
            : [];
    }

    /** Unidades totales (para el badge). */
    public static function count(): int
    {
        return array_sum(self::items());
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function lines(): Collection
    {
        $items = self::items();
        if ($items === []) {
            return collect();
        }

        $products = Catalog::forProducts(array_keys($items));
        $lines = collect();

        foreach ($items as $productId => $qty) {
            $product = $products->get($productId);
            if ($product === null) {
                continue; // producto oculto o sin precio: se ignora
            }
            $subtotal = round($product['price'] * $qty, 2);
            $lines->push([
                'product' => $product,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ]);
        }

        return $lines;
    }

    /** Totales del carrito (IVA incluido) para checkout. */
    public static function totals(): array
    {
        $subtotal = round(self::lines()->sum('subtotal'), 2);
        $iva = round($subtotal * Catalog::IVA_RATE, 2);

        return [
            'subtotal' => $subtotal,
            'iva_rate' => (int) round(Catalog::IVA_RATE * 100),
            'iva' => $iva,
            'total' => round($subtotal + $iva, 2),
        ];
    }

    /** @param array<int, int> $items */
    private static function save(array $items): void
    {
        Session::put(self::KEY, $items);
    }
}
