<?php

namespace Tests\Feature;

use App\Models\InventoryEntry;
use App\Models\Tracking;
use App\Support\LegacyPassword;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthAndShipmentTest extends TestCase
{
    public function test_register_creates_user_and_logs_in(): void
    {
        $handle = 'nuevo_'.Str::lower(Str::random(5));
        $this->post('/registro', [
            'usuario' => $handle,
            'email' => $handle.'@example.com',
            'primerNombre' => 'Ana',
            'primerApellido' => 'Perez',
            'telefono' => '88880000',
            'direccion' => 'Managua',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('register', [
            'name' => $handle,
            'email' => $handle.'@example.com',
            'sTipo' => 'I',
            'sEstado' => 1,
        ]);

        $this->get('/')->assertSessionHas('hermes.auth.user_id');
    }

    public function test_login_with_bcrypt_password(): void
    {
        $handle = 'bcrypt_'.Str::lower(Str::random(5));
        $this->post('/registro', [
            'usuario' => $handle,
            'email' => $handle.'@example.com',
            'primerNombre' => 'Luis',
            'primerApellido' => 'Gomez',
            'telefono' => '88880001',
            'direccion' => 'León',
            'password' => 'secreto456',
            'password_confirmation' => 'secreto456',
        ]);

        $this->post('/logout');

        $this->post('/login', [
            'usuario' => $handle,
            'password' => 'secreto456',
        ])->assertRedirect('/');

        $this->get('/')->assertSessionHas('hermes.auth.user_id');
    }

    public function test_legacy_password_verify_handles_both_formats(): void
    {
        // Formato bcrypt
        $this->assertTrue(LegacyPassword::verify('secreto456', bcrypt('secreto456')));
        $this->assertFalse(LegacyPassword::verify('otra', bcrypt('secreto456')));

        // Formato AES-256-CBC del legacy (clave "HERMES")
        $key = hash('sha256', 'HERMES', true);
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt('clave_aes', 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $stored = base64_encode($iv.$ciphertext);

        $this->assertTrue(LegacyPassword::verify('clave_aes', $stored));
        $this->assertFalse(LegacyPassword::verify('clave_wrong', $stored));
    }

    public function test_shipment_flow_creates_tracking_and_confirm(): void
    {
        // Cliente registrado y logueado
        $handle = 'compra_'.Str::lower(Str::random(5));
        $this->post('/registro', [
            'usuario' => $handle,
            'email' => $handle.'@example.com',
            'primerNombre' => 'Marta',
            'primerApellido' => 'Ruiz',
            'telefono' => '88880002',
            'direccion' => 'Granada',
            'password' => 'secreto789',
            'password_confirmation' => 'secreto789',
        ]);

        // Suscripción activa (el legacy exige membresía para pedir)
        \App\Models\Subscription::query()->create([
            'user_id' => $this->userId(),
            'membership_type' => 'personal',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addMonth()->format('Y-m-d H:i:s'),
        ]);

        // Carrito con producto 1
        $this->post('/carrito/agregar', ['product_id' => 1, 'qty' => 2]);

        // Checkout con envío: empresa 1 y pago 1
        $this->post('/pedido', [
            'shipping' => [1 => ['cia' => 1, 'pago' => 1]],
        ])->assertRedirect(route('shipments.index'));

        $tracking = Tracking::query()->where('idusuario', $this->userId())->first();
        $this->assertNotNull($tracking);
        $this->assertEquals(1, $tracking->estado);
        $this->assertEquals(1, $tracking->idcia);
        $this->assertEquals(1, $tracking->idmetodopago);

        // Elegir destino (ciudad 1)
        $this->post("/mis-envios/{$tracking->id}/destino", ['city' => 1])
            ->assertRedirect();

        $tracking->refresh();
        $this->assertEquals(1, $tracking->idmunicipio);
        $this->assertGreaterThan(0, $tracking->total);

        // Confirmar envío
        $this->post("/mis-envios/{$tracking->id}/confirmar", [
            'name' => 'Marta Ruiz',
            'direccion' => 'Granada',
            'telefono' => '88880002',
            'email' => $handle.'@example.com',
        ])->assertRedirect(route('shipments.show', $tracking->id));

        $tracking->refresh();
        $this->assertNotNull($tracking->codigoenvio);
        $this->assertNotNull($tracking->fechapro);

        $this->assertDatabaseHas('tbl_corresponsal', [
            'idrastro' => $tracking->id,
            'estado' => 1,
        ]);
    }

    public function test_confirm_decrements_stock(): void
    {
        $handle = 'stock_'.Str::lower(Str::random(5));
        $this->post('/registro', [
            'usuario' => $handle,
            'email' => $handle.'@example.com',
            'primerNombre' => 'Sara',
            'primerApellido' => 'Diaz',
            'telefono' => '88880003',
            'direccion' => 'Masaya',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ]);

        \App\Models\Subscription::query()->create([
            'user_id' => $this->userId(),
            'membership_type' => 'personal',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addMonth()->format('Y-m-d H:i:s'),
        ]);

        $this->post('/carrito/agregar', ['product_id' => 1, 'qty' => 2]);
        $this->post('/pedido', ['shipping' => [1 => ['cia' => 1, 'pago' => 1]]]);

        $tracking = Tracking::query()->where('idusuario', $this->userId())->first();
        $this->post("/mis-envios/{$tracking->id}/destino", ['city' => 1]);

        $before = InventoryEntry::available(1);
        $this->post("/mis-envios/{$tracking->id}/confirmar", [
            'name' => 'Sara Diaz',
            'direccion' => 'Masaya',
            'telefono' => '88880003',
            'email' => $handle.'@example.com',
        ]);
        $after = InventoryEntry::available(1);

        $this->assertEquals($before - 2, $after);
    }

    public function test_confirm_rejects_when_stock_is_not_enough(): void
    {
        $handle = 'sin_'.Str::lower(Str::random(5));
        $this->post('/registro', [
            'usuario' => $handle,
            'email' => $handle.'@example.com',
            'primerNombre' => 'Ivan',
            'primerApellido' => 'Lopez',
            'telefono' => '88880004',
            'direccion' => 'Chinandega',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ]);

        \App\Models\Subscription::query()->create([
            'user_id' => $this->userId(),
            'membership_type' => 'personal',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'end_date' => now()->addMonth()->format('Y-m-d H:i:s'),
        ]);

        // Agotar el stock del producto 1.
        InventoryEntry::query()->create([
            'idproducto' => 1,
            'ncantidad' => 1000,
            'nestado' => 1,
            'stipo' => 'S',
            'dfecha' => now()->toDateString(),
        ]);

        $this->post('/carrito/agregar', ['product_id' => 1, 'qty' => 2]);
        $this->post('/pedido', ['shipping' => [1 => ['cia' => 1, 'pago' => 1]]]);

        $tracking = Tracking::query()->where('idusuario', $this->userId())->first();
        $this->post("/mis-envios/{$tracking->id}/destino", ['city' => 1]);

        $this->post("/mis-envios/{$tracking->id}/confirmar", [
            'name' => 'Ivan Lopez',
            'direccion' => 'Chinandega',
            'telefono' => '88880004',
            'email' => $handle.'@example.com',
        ])->assertSessionHasErrors('name');

        $tracking->refresh();
        $this->assertNull($tracking->codigoenvio);
    }

    private function userId(): int
    {
        return (int) session('hermes.auth.user_id');
    }
}