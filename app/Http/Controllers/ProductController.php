<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Catalog;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function show(int $product): Response
    {
        $data = Catalog::productById($product);

        abort_if($data === null, 404);

        return Inertia::render('Product', [
            'product' => $data,
            'categories' => Catalog::categories(),
        ]);
    }
}
