<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 * @property float price
 * @property int count
 * @property Category[] categories
 */
class Product extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $attributes = [
        'price' => 0.0,
        'count' => 0,
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }
}
