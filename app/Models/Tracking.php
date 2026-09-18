<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Seguimiento de envíos -> tabla `tbl_rastreo`.
 * Es el flujo de "pedido con envío" del legacy: cada fila es un producto
 * en proceso de envío (empresa, método de pago, destino, estados).
 */
class Tracking extends Model
{
    protected $table = 'tbl_rastreo';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'idusuario' => 'integer',
        'idproducto' => 'integer',
        'idcia' => 'integer',
        'idmetodopago' => 'integer',
        'idmunicipio' => 'integer',
        'cantidad' => 'integer',
        'precio' => 'float',
        'iva' => 'float',
        'precioI' => 'float',
        'preciot' => 'float',
        'subtotal' => 'float',
        'total' => 'float',
        'precioenvio' => 'float',
        'estado' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'idproducto', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idusuario', 'id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(TrackingCompany::class, 'idcia', 'id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'idmetodopago', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'idmunicipio', 'id');
    }

    public function corresponsal(): HasOne
    {
        // Varias filas por envío (histórico): mostrar siempre la más reciente.
        return $this->hasOne(Corresponsal::class, 'idrastro', 'id')->latestOfMany();
    }
}