<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Videos de YouTube -> tabla `youtube_videos`. */
class Video extends Model
{
    protected $table = 'youtube_videos';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'start_time' => 'integer',
        'end_time' => 'integer',
    ];
}