<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Páginas del sistema -> tabla `pages`. */
class Page extends Model
{
    protected $table = 'pages';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];
}