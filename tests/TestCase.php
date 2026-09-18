<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Cada test corre en una transacción que se revierte: los escritos
        // (pedidos, stock en tbl_entrada, clientes…) nunca contaminan el
        // testing.sqlite compartido entre ejecuciones.
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();

        parent::tearDown();
    }
}