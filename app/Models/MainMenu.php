<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Menú principal -> tabla `tbl_main_menu`.
 */
class MainMenu extends Model
{
    protected $table = 'tbl_main_menu';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function submenus(): HasMany
    {
        return $this->hasMany(Submenu::class, 'main_menu_id', 'id');
    }
}
