<?php

namespace App\Http\Controllers;

use App\Models\Slide;
use App\Support\Catalog;
use App\Support\Content;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');

        return Inertia::render('Home', [
            'products' => Catalog::products(search: $search),
            'categories' => Catalog::categories(),
            'slides' => Slide::query()
                ->orderBy('id')
                ->get()
                ->map(fn (Slide $s) => ['url' => $s->url, 'caption' => $s->caption]),
            'video' => Content::randomVideo(),
            'search' => $search,
        ]);
    }
}
