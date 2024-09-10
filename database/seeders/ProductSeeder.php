<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory(50)
            ->create()
            ->each(function (Product $product) {
                $product->categories()->saveMany(Category::factory(rand(1, 5))->create())->make();
            });
    }
}
