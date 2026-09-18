<?php

namespace App\Support;

use App\Models\Bank;
use App\Models\City;
use App\Models\PaymentMethod;
use App\Models\TrackingCompany;
use Illuminate\Support\Collection;

/** Datos de referencia para combos/selects (mapean tablas legacy). */
class Lookup
{
    public static function cities(): Collection
    {
        return City::query()
            ->where('sEstado', '!=', 0)
            ->orderBy('sCiudad')
            ->get()
            ->map(fn (City $c) => ['id' => (int) $c->id, 'name' => (string) $c->sCiudad]);
    }

    public static function shippingCompanies(): Collection
    {
        return TrackingCompany::query()
            ->where('estado', '!=', 0)
            ->orderBy('scompany')
            ->get()
            ->map(fn (TrackingCompany $t) => ['id' => (int) $t->id, 'name' => (string) $t->scompany]);
    }

    public static function paymentMethods(): Collection
    {
        return PaymentMethod::query()
            ->where('estado', '!=', 0)
            ->orderBy('pago')
            ->get()
            ->map(fn (PaymentMethod $p) => ['id' => (int) $p->id, 'name' => (string) $p->pago]);
    }

    public static function banks(): Collection
    {
        return Bank::query()
            ->where('estado', '!=', 0)
            ->orderBy('banco')
            ->get()
            ->map(fn (Bank $b) => ['id' => (int) $b->id, 'name' => (string) $b->banco]);
    }
}