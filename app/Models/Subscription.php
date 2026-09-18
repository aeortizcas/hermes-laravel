<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Suscripciones de clientes -> tabla `subscriptions`. */
class Subscription extends Model
{
    protected $table = 'subscriptions';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
    ];
}