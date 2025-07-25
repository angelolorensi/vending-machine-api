<?php

namespace App\Services;

use App\Models\Product;
use App\Exceptions\NotFoundException;
use Illuminate\Support\Collection;

class ProductService
{
    public function getAllProducts(): Collection
    {
        return Product::with(['productCategory', 'slots'])->get();
    }

    public function getProductById(int $id): Product
    {
        $product = Product::with(['productCategory', 'slots'])->find($id);

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
