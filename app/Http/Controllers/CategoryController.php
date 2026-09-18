<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Support\Catalog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function show(Request $request, int $category): Response
    {
        $categoryModel = ProductCategory::query()->active()->find($category);

        abort_unless($categoryModel !== null, 404);

        $search = (string) $request->query('search', '');

        return Inertia::render('Category', [
            'category' => [
                'id' => (int) $categoryModel->id,
                'name' => (string) $categoryModel->name,
            ],
            'products' => Catalog::products(categoryId: $categoryModel->id, search: $search),
            'categories' => Catalog::categories(),
            'search' => $search,
        ]);
    }
}
