<?php

namespace App\Support;

use App\Models\MainMenu;
use App\Models\Video;

/** Contenido dinámico de la tienda (videos, menús) -> tablas legacy. */
class Content
{
    /** Video aleatorio (igual que el legacy: ORDER BY RAND() LIMIT 1). */
    public static function randomVideo(): ?array
    {
        $video = Video::query()->inRandomOrder()->first();
        if (! $video) {
            return null;
        }

        return [
            'id' => (int) $video->id,
            'video_id' => (string) $video->video_id,
            'title' => (string) $video->title,
            'start' => (int) $video->start_time,
            'end' => (int) $video->end_time,
        ];
    }

    /** Menú principal con submenús (tbl_main_menu + tbl_submenu). */
    public static function menu(): array
    {
        return MainMenu::query()
            ->orderBy('id')
            ->get()
            ->map(function (MainMenu $m) {
                $submenus = $m->submenus->map(fn ($s) => [
                    'name' => (string) $s->name,
                    'url' => (string) $s->url,
                ]);

                return [
                    'id' => (int) $m->id,
                    'name' => (string) $m->name,
                    'submenus' => $submenus,
                ];
            })
            ->values()
            ->all();
    }
}