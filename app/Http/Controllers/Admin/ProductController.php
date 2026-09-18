<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryEntry;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::query()
            ->with('category')
            ->orderByDesc('id')
            ->get()
            ->map(function (Product $p) {
                $price = Sale::query()
                    ->where('idproducto', $p->id)
                    ->where('CV', 'V')
                    ->where('cestado', 1)
                    ->orderByDesc('idventa')
                    ->value('nprecio');

                $stockIn = InventoryEntry::query()
                    ->where('idproducto', $p->id)
                    ->where('nestado', 1)
                    ->where('stipo', 'E')
                    ->sum('ncantidad');

                $stockOut = InventoryEntry::query()
                    ->where('idproducto', $p->id)
                    ->where('nestado', 1)
                    ->where('stipo', 'S')
                    ->sum('ncantidad');

                return [
                    'id' => (int) $p->id,
                    'name' => (string) $p->sProducto,
                    'category' => $p->category?->name,
                    'price' => $price !== null ? round((float) $price, 2) : null,
                    'stock' => max(0, (int) $stockIn - (int) $stockOut),
                    'estado' => (int) $p->estado,
                ];
            })
            ->values();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => null,
            'categories' => ProductCategory::query()->active()->orderBy('name')->get()
                ->map(fn ($c) => ['id' => (int) $c->id, 'name' => (string) $c->name])
                ->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:tbl_categories,id'],
            'unit' => ['nullable', 'string', 'max:45'],
            'code' => ['nullable', 'string', 'max:45'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $product = Product::query()->create([
            'sProducto' => $data['name'],
            'sDescripcion' => $data['description'] ?? null,
            'idproveedor' => null,
            'unidad' => $data['unit'] ?? null,
            'scodigo' => $data['code'] ?? null,
            'estado' => 1,
            'idec' => $data['category_id'] ?? null,
        ]);

        $this->syncCatalogData($product, $data);

        return redirect()->route('admin.products.index')->with('success', 'Producto creado.');
    }

    public function edit(int $product): Response
    {
        $p = Product::query()->with('images')->findOrFail($product);

        $price = Sale::query()
            ->where('idproducto', $p->id)
            ->where('CV', 'V')
            ->where('cestado', 1)
            ->orderByDesc('idventa')
            ->value('nprecio');

        $stockIn = InventoryEntry::query()
            ->where('idproducto', $p->id)
            ->where('nestado', 1)
            ->where('stipo', 'E')
            ->sum('ncantidad');

        $stockOut = InventoryEntry::query()
            ->where('idproducto', $p->id)
            ->where('nestado', 1)
            ->where('stipo', 'S')
            ->sum('ncantidad');

        return Inertia::render('Admin/Products/Form', [
            'product' => [
                'id' => (int) $p->id,
                'name' => (string) $p->sProducto,
                'description' => (string) $p->sDescripcion,
                'category_id' => $p->idec !== null ? (int) $p->idec : null,
                'unit' => (string) $p->unidad,
                'code' => (string) $p->scodigo,
                'price' => $price !== null ? round((float) $price, 2) : null,
                'stock' => max(0, (int) $stockIn - (int) $stockOut),
                'estado' => (int) $p->estado,
                'images' => $p->images->map(fn ($img) => [
                    'id' => (int) $img->id,
                    'url' => $img->url,
                    'portada' => (int) $img->portada,
                ])->values(),
            ],
            'categories' => ProductCategory::query()->active()->orderBy('name')->get()
                ->map(fn ($c) => ['id' => (int) $c->id, 'name' => (string) $c->name])
                ->values(),
        ]);
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $p = Product::query()->findOrFail($product);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:tbl_categories,id'],
            'unit' => ['nullable', 'string', 'max:45'],
            'code' => ['nullable', 'string', 'max:45'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'estado' => ['nullable', 'in:0,1'],
        ]);

        $p->update([
            'sProducto' => $data['name'],
            'sDescripcion' => $data['description'] ?? null,
            'unidad' => $data['unit'] ?? null,
            'scodigo' => $data['code'] ?? null,
            'estado' => $data['estado'] ?? $p->estado,
            'idec' => $data['category_id'] ?? null,
        ]);

        $this->syncCatalogData($p, $data, update: true);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(int $product): RedirectResponse
    {
        $p = Product::query()->findOrFail($product);
        $p->update(['estado' => 0]);

        return redirect()->route('admin.products.index')->with('success', 'Producto desactivado.');
    }

    /**
     * Aplica precio vigente (tbl_venta cestado=1) y ajusta stock
     * (tbl_entrada stipo='E').
     */
    private function syncCatalogData(Product $product, array $data, bool $update = false): void
    {
        $price = (float) $data['price'];

        if (! $update) {
            Sale::query()->create([
                'nprecio' => $price,
                'dfecha' => now()->toDateString(),
                'CV' => 'V',
                'cestado' => 1,
                'idproducto' => $product->id,
            ]);
        } else {
            // Nueva fila vigente; las anteriores quedan como histórico (cestado=0).
            Sale::query()
                ->where('idproducto', $product->id)
                ->where('CV', 'V')
                ->where('cestado', 1)
                ->update(['cestado' => 0]);

            Sale::query()->create([
                'nprecio' => $price,
                'dfecha' => now()->toDateString(),
                'CV' => 'V',
                'cestado' => 1,
                'idproducto' => $product->id,
            ]);
        }

        if (array_key_exists('stock', $data) && $data['stock'] !== null) {
            // El stock se administra como "entradas": se registra un ajuste
            // restando las salidas previas para dejar el saldo exacto.
            $current = max(0, (int) InventoryEntry::query()
                ->where('idproducto', $product->id)
                ->where('nestado', 1)
                ->where('stipo', 'E')
                ->sum('ncantidad') - (int) InventoryEntry::query()
                ->where('idproducto', $product->id)
                ->where('nestado', 1)
                ->where('stipo', 'S')
                ->sum('ncantidad'));

            $delta = (int) $data['stock'] - $current;
            if ($delta > 0) {
                InventoryEntry::query()->create([
                    'idproducto' => $product->id,
                    'ncantidad' => $delta,
                    'nestado' => 1,
                    'stipo' => 'E',
                    'dfecha' => now()->toDateString(),
                ]);
            } elseif ($delta < 0) {
                InventoryEntry::query()->create([
                    'idproducto' => $product->id,
                    'ncantidad' => abs($delta),
                    'nestado' => 1,
                    'stipo' => 'S',
                    'dfecha' => now()->toDateString(),
                ]);
            }
        }

        if (request()->hasFile('cover')) {
            $file = request()->file('cover');
            $name = $product->id.'-'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $name);

            // Si es la primera imagen del producto, es portada.
            $isFirst = ProductImage::query()->where('idproducto', $product->id)->count() === 0;

            ProductImage::query()->create([
                'images' => $name,
                'date_time' => now()->format('Y-m-d H:i:s'),
                'idproducto' => $product->id,
                'portada' => $isFirst ? 1 : 0,
                'cEstado' => 1,
            ]);
        }
    }
}