<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    const FIND_LIMIT = 10;

    public function show(Product $product)
    {
        return $product;
    }

    public function find(Request $request)
    {
        return Product::query()
            ->filter($request->filter, $request->resourceId)
            ->limit(self::FIND_LIMIT)
            ->get();
    }

    public function attach(Product $product, int $categoryId)
    {
        $product->addCategory($categoryId);
    }

    public function detach(Product $product, int $categoryId)
    {
        $product->removeCategory($categoryId);
    }

    public function changeIndex(Request $request, Product $product, int $categoryId)
    {
        $product->changeIndex($categoryId, $request->index);
    }
}
