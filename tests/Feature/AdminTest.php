<?php

namespace Tests\Feature;

use App\Models\InventoryEntry;
use App\Models\Sale;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_admin_login_with_legacy_credentials(): void
    {
        $this->post('/admin/login', [
            'usuario' => 'ACER',
            'password' => 'LA',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get('/admin')->assertOk()->assertSee('Dashboard');
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin/productos')->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_product_with_price_and_stock(): void
    {
        $this->post('/admin/login', ['usuario' => 'ACER', 'password' => 'LA']);

        $name = 'Producto Admin '.Str::random(4);

        $this->post('/admin/productos', [
            'name' => $name,
            'description' => 'Creado en prueba admin',
            'category_id' => 1,
            'unit' => 'unidad',
            'price' => 25.5,
            'stock' => 10,
        ])->assertRedirect(route('admin.products.index'));

        $product = \App\Models\Product::query()->where('sProducto', $name)->first();
        $this->assertNotNull($product);
        $this->assertEquals(1, $product->idec);

        // Precio vigente en tbl_venta (cestado=1, CV='V')
        $this->assertDatabaseHas('tbl_venta', [
            'idproducto' => $product->id,
            'nprecio' => 25.5,
            'cestado' => 1,
            'CV' => 'V',
        ]);

        // Stock: entrada en tbl_entrada (stipo='E')
        $this->assertDatabaseHas('tbl_entrada', [
            'idproducto' => $product->id,
            'ncantidad' => 10,
            'stipo' => 'E',
            'nestado' => 1,
        ]);

        // Sin portada no aparece en la lista pública (regla legacy: exige
        // precio vigente + imagen de portada), aunque el detalle existe.
        $visible = \App\Support\Catalog::products()->pluck('name')->contains($name);
        $this->assertFalse($visible);
    }

    public function test_admin_can_update_price_and_stock(): void
    {
        $this->post('/admin/login', ['usuario' => 'ACER', 'password' => 'LA']);

        $name = 'Actualizar '.Str::random(4);
        $this->post('/admin/productos', [
            'name' => $name,
            'price' => 10,
            'stock' => 5,
        ]);

        $product = \App\Models\Product::query()->where('sProducto', $name)->first();

        $this->put("/admin/productos/{$product->id}", [
            'name' => $name,
            'price' => 12.75,
            'stock' => 8,
            'estado' => 1,
        ])->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('tbl_venta', [
            'idproducto' => $product->id,
            'nprecio' => 12.75,
            'cestado' => 1,
        ]);

        $entradas = InventoryEntry::query()
            ->where('idproducto', $product->id)
            ->where('nestado', 1)
            ->where('stipo', 'E')
            ->sum('ncantidad');
        $salidas = InventoryEntry::query()
            ->where('idproducto', $product->id)
            ->where('nestado', 1)
            ->where('stipo', 'S')
            ->sum('ncantidad');

        $this->assertEquals(8, $entradas - $salidas);
    }

    public function test_admin_can_deactivate_product(): void
    {
        $this->post('/admin/login', ['usuario' => 'ACER', 'password' => 'LA']);

        $name = 'Desactivar '.Str::random(4);
        $this->post('/admin/productos', ['name' => $name, 'price' => 5]);
        $product = \App\Models\Product::query()->where('sProducto', $name)->first();

        $this->delete("/admin/productos/{$product->id}")
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('tbl_producto', ['id' => $product->id, 'estado' => 0]);
    }

    public function test_admin_catalog_crud(): void
    {
        $this->post('/admin/login', ['usuario' => 'ACER', 'password' => 'LA']);

        $name = 'Cat Admin '.Str::random(4);
        $this->post('/admin/catalogos/categorias', ['name' => $name])
            ->assertRedirect();

        $this->assertDatabaseHas('tbl_categories', ['name' => $name, 'estado' => 1]);

        $cat = \App\Models\ProductCategory::query()->where('name', $name)->first();
        $this->delete("/admin/catalogos/categorias/{$cat->id}")->assertRedirect();
        $this->assertDatabaseHas('tbl_categories', ['id' => $cat->id, 'estado' => 0]);
    }
}