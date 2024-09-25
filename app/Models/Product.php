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
 * @property float price
 * @property int count
 * @property Category[]|object categories
 */
class Product extends Model
{
    use HasFactory;
    use ArrayTrait;

    public $timestamps = false;

    protected $attributes = [
        'price' => 0.0,
        'count' => 0,
    ];

    public function categories(string $sort = 'asc')
    {
        return $this->belongsToMany(Category::class, 'product_categories')->withPivot('index')->orderByPivot('index', $sort);
    }

    public function addCategory($categoryId): Category
    {
        if ($this->categories->contains($categoryId)) {
            throw new Exception('Category already attached');
        }

        $this->categories()->attach($categoryId, ['index' => $this->categories()->count()]);
        $this->updateIndex();

        return $this->categories('desc')->first();
    }

    public function removeCategory($categoryId)
    {
        $this->categories()->detach($categoryId);
    }

    public function clearCategories()
    {
        $this->categories()->detach();
    }

    public function changeIndex(int $categoryId, int $newIndex)
    {
        $categories = $this->categories->toArray();
        $categoryIndex = self::findIndexInArray($categories, 'id', $categoryId);

        self::moveElement($categories, $categoryIndex, $newIndex);
        $this->updateIndex($categories);
    }

    private function updateIndex(array $categories = null)
    {
        foreach ($categories ?? $this->categories->toArray() as $index => $category) {
            $this->categories()->updateExistingPivot($category['id'], ['index' => $index]);
        }
    }

    public function updatePivot(int $categoryId, array $newValues): array
    {
        if (isset($newValues['index'])) {
            $this->changeIndex($categoryId, $newValues['index']);
            unset($newValues['index']);
        }

        if ($newValues) {
            $this->categories()->updateExistingPivot($categoryId, $newValues);
        }

        return $this->categories()->get()->toArray();
    }

    public function scopeFilter(Builder $query, ?string $filter, ?int $categoryId)
    {
        $query->when(isset($filter), function (Builder $q) use ($filter) {
            $q->where(function (Builder $q) use ($filter) {
                $q->where('name', 'like', "%$filter%")
                    ->orWhere('id', $filter);
            });
        });

        $query->when(isset($categoryId), function (Builder $q) use ($categoryId) {
            $q->whereNot(function (Builder $q) use ($categoryId) {
                $q->whereHas('categories', function (Builder $q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            });
        });

        return $query;
    }
}
