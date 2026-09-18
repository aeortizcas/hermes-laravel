<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Permisos de usuario sobre páginas -> tabla `user_page_permissions` (niveles). */
class PagePermission extends Model
{
    protected $table = 'user_page_permissions';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'page_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}