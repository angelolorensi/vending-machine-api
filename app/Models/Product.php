<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $table = 'products';

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'product_category_id');
    }

    public function slots()
    {
        return $this->hasMany(Slot::class, 'product_id', 'product_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'product_id', 'product_id');
    }
}
