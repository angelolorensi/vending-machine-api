<?php

namespace App\Services;

use App\Models\Product;
use App\Exceptions\NotFoundException;

class ProductService
{
    public function getProductById(int $id): Product
    {
        $product = Product::with(['category', 'slots'])->find($id);

        if (!$product) {
            throw new NotFoundException('Product not found');
        }

        return $product;
    }

    public function createProduct(array $data): Product
    {
        return Product::create($data);
    }

    public function updateProduct(int $id, array $data): Product
    {
        $product = Product::find($id);

        if (!$product) {
            throw new NotFoundException('Product not found');
        }

        $product->update($data);
        return $product->fresh();
    }

    public function deleteProduct(int $id): bool
    {
        $product = Product::find($id);

        if (!$product) {
            throw new NotFoundException('Product not found');
        }

        return $product->delete();
    }
}
