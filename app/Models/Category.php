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

    public function products(string $sort = 'asc')
    {
        return $this->belongsToMany(Product::class, 'product_categories')->withPivot('index')->orderByPivot('index', $sort);
    }

    public function addProduct($productId): Product
    {
        if ($this->products->contains($productId)) {
            throw new Exception('Product already attached');
        }

        $this->products()->attach($productId, ['index' => $this->products()->count()]);
        $this->updateIndex();

        return $this->products('desc')->first();
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
        $this->updateIndex($products);
    }

    private function updateIndex(array $products = null)
    {
        foreach ($products ?? $this->products->toArray() as $index => $product) {
            $this->products()->updateExistingPivot($product['id'], ['index' => $index]);
        }
    }

    public function updatePivot(int $productId, array $newValues): array
    {
        if (isset($newValues['index'])) {
            $this->changeIndex($productId, $newValues['index']);
            unset($newValues['index']);
        }

        if ($newValues) {
            $this->products()->updateExistingPivot($productId, $newValues);
        }

        return $this->products()->get()->toArray();
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
