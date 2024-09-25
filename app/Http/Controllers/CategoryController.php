<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    const FIND_LIMIT = 10;

    public function show(Category $category)
    {
        return $category;
    }

    public function find(Request $request)
    {
        return Category::query()
            ->filter($request->filter, $request->resourceId)
            ->limit(self::FIND_LIMIT)
            ->get();
    }

    public function attach(Category $category, int $productId)
    {
        return $category->addProduct($productId);
    }

    public function detach(Category $category, int $productId)
    {
        $category->removeProduct($productId);
    }

    public function changeIndex(Request $request, Category $category, int $productId)
    {
        $category->changeIndex($productId, $request->index);

        return $category->products()->get()->toArray();
    }

    public function updatePivot(Request $request, Category $category, int $productId)
    {
        return $category->updatePivot($productId, $request->newValues);
    }
}
