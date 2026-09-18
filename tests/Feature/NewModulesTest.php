<?php

namespace Tests\Feature;

use App\Models\Bank;
use App\Models\Currency;
use App\Models\Page;
use App\Models\PagePermission;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewModulesTest extends TestCase
{
    private function loginAdmin(): void
    {
        $this->post('/admin/login', ['usuario' => 'ACER', 'password' => 'LA']);
    }

    private function registerUser(string $handle): void
    {
        $this->post('/registro', [
            'usuario' => $handle,
            'email' => $handle.'@example.com',
            'primerNombre' => 'Tester',
            'primerApellido' => 'Nuevo',
            'telefono' => '88889999',
            'direccion' => 'Managua',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ]);
    }

    public function test_admin_catalog_bancos_and_monedas_crud(): void
    {
        $this->loginAdmin();

        // Crear moneda
        $monedaName = 'Moneda '.Str::random(4);
        $this->post('/admin/catalogos/monedas', ['moneda' => $monedaName])->assertRedirect();
        $moneda = Currency::query()->where('moneda', $monedaName)->first();
        $this->assertNotNull($moneda);

        // Crear banco con esa moneda
        $bancoName = 'Banco '.Str::random(4);
        $this->post('/admin/catalogos/bancos', [
            'banco' => $bancoName,
            'idmoneda' => (string) $moneda->id,
        ])->assertRedirect();
        $banco = Bank::query()->where('banco', $bancoName)->first();
        $this->assertNotNull($banco);
        $this->assertEquals(1, $banco->estado);
        $this->assertEquals($moneda->id, $banco->idmoneda);

        // Editar banco
        $this->put("/admin/catalogos/bancos/{$banco->id}", ['banco' => $bancoName.' 2', 'idmoneda' => (string) $moneda->id])
            ->assertRedirect();
        $this->assertEquals($bancoName.' 2', $banco->fresh()->banco);

        // Desactivar
        $this->delete("/admin/catalogos/bancos/{$banco->id}")->assertRedirect();
        $this->assertEquals(0, $banco->fresh()->estado);
    }

    public function test_admin_catalog_requires_login(): void
    {
        $this->get('/admin/catalogos/bancos')->assertRedirect(route('admin.login'));
    }

    public function test_subscription_page_and_verifier(): void
    {
        $handle = 'subs_'.Str::lower(Str::random(5));
        $this->registerUser($handle);

        // Sin membresía, /pedido redirige a /suscripcion (verificador legacy)
        $this->post('/carrito/agregar', ['product_id' => 1, 'qty' => 1]);
        $this->post('/pedido', ['shipping' => [1 => ['cia' => 1, 'pago' => 1]]])
            ->assertRedirect(route('subscriptions.index'));

        // Página de membresías
        $this->get('/suscripcion')->assertOk()
            ->assertInertia(fn ($page) => $page->component('Subscription/Index'));

        // Suscribirse
        $this->post('/suscripcion', ['membership' => 'personal'])->assertRedirect();
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => (int) session('hermes.auth.user_id'),
            'membership_type' => 'personal',
        ]);

        // Ahora /pedido sí avanza
        $this->post('/pedido', ['shipping' => [1 => ['cia' => 1, 'pago' => 1]]])
            ->assertRedirect(route('shipments.index'));
    }

    public function test_admin_pages_and_niveles(): void
    {
        $this->loginAdmin();

        // Crear página
        $name = 'Pagina '.Str::random(4);
        $this->post('/admin/paginas', ['name' => $name, 'description' => 'desc'])->assertRedirect();
        $page = Page::query()->where('name', $name)->first();
        $this->assertNotNull($page);

        // Lista
        $this->get('/admin/paginas')->assertOk()
            ->assertInertia(fn ($p) => $p->component('Admin/Pages/Index'));

        // Asignar nivel a usuario 1
        $this->post('/admin/paginas/niveles', [
            'user_id' => 1,
            'page_id' => $page->id,
            'access_type' => 'allow',
        ])->assertRedirect();
        $this->assertDatabaseHas('user_page_permissions', [
            'user_id' => 1,
            'page_id' => $page->id,
            'access_type' => 'allow',
        ]);

        $perm = PagePermission::query()->where('page_id', $page->id)->first();
        $this->assertNotNull($perm);
        $this->delete("/admin/paginas/niveles/{$perm->id}")->assertRedirect();
        $this->assertDatabaseMissing('user_page_permissions', ['id' => $perm->id]);

        // Eliminar página
        $this->delete("/admin/paginas/{$page->id}")->assertRedirect();
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_invoice_page_requires_owner_or_admin(): void
    {
        // Sin sesión -> 403
        $this->get('/mis-envios/1/factura')->assertStatus(403);

        $this->loginAdmin();
        // Admin puede ver cualquier factura
        $this->get('/admin/envios/1/factura')->assertOk()
            ->assertInertia(fn ($page) => $page->component('Invoice/Show'));
    }
}