<?php

namespace Tests\Feature;

use App\Models\Tracking;
use Tests\TestCase;

class AdminShipmentsTest extends TestCase
{
    private function loginAdmin(): void
    {
        $this->post('/admin/login', ['usuario' => 'ACER', 'password' => 'LA']);
    }

    public function test_admin_shipment_list_requires_login(): void
    {
        $this->get('/admin/envios')->assertRedirect(route('admin.login'));
    }

    public function test_admin_advances_shipment_through_stages(): void
    {
        $this->loginAdmin();

        $this->get('/admin/envios')->assertOk()
            ->assertInertia(fn ($page) => $page->component('Admin/Shipments/Index'));

        // Tomamos un envío real del dump que aún no esté procesado
        $tracking = Tracking::query()
            ->where('estado', 1)
            ->whereNull('fechapro')
            ->first();

        $this->assertNotNull($tracking, 'No hay envíos pendientes en el dump');

        // Etapa 1: procesar
        $this->post("/admin/envios/{$tracking->id}/avanzar", ['action' => 'procesar'])
            ->assertRedirect();
        $tracking->refresh();
        $this->assertNotNull($tracking->fechapro);

        // Etapa 2: encargar al courier (fechaencargo + codigoenviocia)
        $this->post("/admin/envios/{$tracking->id}/avanzar", [
            'action' => 'encargar',
            'codigoenviocia' => 'TEST-COUR-'.time(),
        ])->assertRedirect();
        $tracking->refresh();
        $this->assertNotNull($tracking->fechaencargo);
        $this->assertNotNull($tracking->codigoenviocia);

        // Etapa 3: entregar
        $this->post("/admin/envios/{$tracking->id}/avanzar", ['action' => 'entregar'])
            ->assertRedirect();
        $tracking->refresh();
        $this->assertNotNull($tracking->fechaentrega);

        // Detalle accesible
        $this->get("/admin/envios/{$tracking->id}")->assertOk();
    }
}