<?php

namespace App\Models;

use App\Traits\ArrayTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 * @property Product[]|object products
 */
class Category extends Model
{
    use HasFactory;
    use ArrayTrait;

    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories')->withPivot('index')->orderByPivot('index');
    }

    public function addProduct($productId)
    {
        if ($this->products->contains($productId)) {
            throw new Exception('Product already attached');
        }

        $this->products()->attach($productId, ['index' => $this->products()->count()]);
    }

    public function removeProduct($productId)
    {
        $this->products()->detach($productId);
    }

    public function clearProducts()
    {
        $this->products()->detach();
    }

    public function changeIndex(int $productId, int $newIndex)
    {
        $products = $this->products->toArray();
        $productIndex = self::findIndexInArray($products, 'id', $productId);

        self::moveElement($products, $productIndex, $newIndex);

        foreach ($products as $index => $product) {
            $this->products()->updateExistingPivot($product['id'], ['index' => $index]);
        }
    }

    public function scopeFilter(Builder $query, ?string $filter, ?int $productId)
    {
        $query->when(isset($filter), function (Builder $q) use ($filter) {
            $q->where(function (Builder $q) use ($filter) {
                $q->where('name', 'like', "%$filter%")
                    ->orWhere('id', $filter);
            });
        });
        
        $query->when(isset($productId), function (Builder $q) use ($productId) {
            $q->whereNot(function (Builder $q) use ($productId) {
                $q->whereHas('products', function (Builder $q) use ($productId) {
                    $q->where('product_id', $productId);
                });
            });
        });

        return $query;
    }
}
