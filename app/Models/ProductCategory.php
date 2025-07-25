<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';
    protected $primaryKey = 'product_category_id';

    protected $fillable = [
        'name',
        'color'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_category_id', 'product_category_id');
    }
}
