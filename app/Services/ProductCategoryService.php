<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Exceptions\NotFoundException;

class ProductCategoryService
{
    public function getProductCategoryById(int $id): ProductCategory
    {
        $productCategory = ProductCategory::with('products')->find($id);

        if (!$productCategory) {
            throw new NotFoundException('Product category not found');
        }

        return $productCategory;
    }

    public function createProductCategory(array $data): ProductCategory
    {
        return ProductCategory::create($data);
    }

    public function updateProductCategory(int $id, array $data): ProductCategory
    {
        $productCategory = ProductCategory::find($id);

        if (!$productCategory) {
            throw new NotFoundException('Product category not found');
        }

        $productCategory->update($data);
        return $productCategory->fresh();
    }

    public function deleteProductCategory(int $id): bool
    {
        $productCategory = ProductCategory::find($id);

        if (!$productCategory) {
            throw new NotFoundException('Product category not found');
        }

        return $productCategory->delete();
    }
}
