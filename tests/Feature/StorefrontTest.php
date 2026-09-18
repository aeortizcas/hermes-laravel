<?php

namespace Tests\Feature;

use App\Models\OrderMaster;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    public function test_home_page_lists_real_products(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Hermes')
            ->assertSee('Pildora');
    }

    public function test_product_detail_page(): void
    {
        $this->get('/producto/1')
            ->assertOk()
            ->assertSee('Pildora');
    }

    public function test_category_page_filters_products(): void
    {
        $this->get('/categorias/1')
            ->assertOk();
    }

    public function test_cart_flow_adds_and_shows_item(): void
    {
        $this->post('/carrito/agregar', ['product_id' => 1, 'qty' => 2])
            ->assertRedirect();

        $this->get('/carrito')
            ->assertOk()
            ->assertSee('Pildora');
    }

    public function test_checkout_persists_legacy_order_rows(): void
    {
        $this->post('/carrito/agregar', ['product_id' => 1, 'qty' => 1]);

        $this->post('/checkout', [
            'name' => 'Cliente Prueba',
            'email' => 'prueba@example.com',
            'telefono' => '88888888',
        ])->assertOk()
            ->assertSee('Cliente Prueba');

        $this->assertDatabaseHas('tbl_master', [
            'cestado' => 1,
            'sbi' => 0,
        ]);

        $master = OrderMaster::query()->orderByDesc('idmaster')->first();
        $this->assertNotNull($master);

        $this->assertDatabaseHas('tbl_detalle', [
            'idmaestro' => $master->idmaster,
            'idproducto' => 1,
            'ncantidad' => 1,
            'cestado' => 1,
            'Y' => 1,
        ]);
    }
}
