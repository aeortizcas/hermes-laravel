<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Imágenes del slideshow de portada -> tabla `images`.
 * Los archivos se sirven desde public/uploads/slides.
 */
class Slide extends Model
{
    protected $table = 'images';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        $file = basename((string) $this->image_url);

        return asset('uploads/slides/'.rawurlencode($file));
    }
}
