<?php

namespace App\Http\Controllers;

use App\Models\Tracking;
use App\Support\AuthSession;
use App\Support\Shipment;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Factura imprimible por envío (alternativa HTML al FPDF del legacy).
 * Accesible desde el admin (cualquier envío) y desde el cliente (los suyos).
 */
class InvoiceController extends Controller
{
    public function show(int $tracking): Response
    {
        $model = Tracking::query()->findOrFail($tracking);

        // Admin autenticado puede ver cualquier factura; el cliente solo las suyas.
        $admin = \App\Support\AdminSession::user();
        $user = AuthSession::user();

        if (! $admin && (! $user || (int) $model->idusuario !== (int) $user->id)) {
            abort(403);
        }

        $corresponsal = $model->corresponsal;

        return Inertia::render('Invoice/Show', [
            'shipment' => Shipment::serialize($model),
            'corresponsal' => $corresponsal ? [
                'nombre' => (string) $corresponsal->nombre,
                'Direccion' => (string) ($corresponsal->Direccion ?? ''),
                'telefono' => (string) ($corresponsal->telefono ?? ''),
                'email' => (string) ($corresponsal->email ?? ''),
                'banco' => (string) ($corresponsal->banco ?? ''),
                'numerocta' => (string) ($corresponsal->numerocta ?? ''),
            ] : null,
            'cliente' => $model->user ? [
                'name' => (string) $model->user->name,
                'email' => (string) $model->user->email,
                'telefono' => (string) $model->user->sTelefono,
                'direccion' => (string) $model->user->sDireccion,
            ] : null,
        ]);
    }
}