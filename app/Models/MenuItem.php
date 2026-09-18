<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Ítems de menú -> tabla `menu_items` (menú lateral por categoría). */
class MenuItem extends Model
{
    protected $table = 'menu_items';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'category_id' => 'integer',
    ];
}