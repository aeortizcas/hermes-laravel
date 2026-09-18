<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Submenú -> tabla `tbl_submenu`.
 */
class Submenu extends Model
{
    protected $table = 'tbl_submenu';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];
}
