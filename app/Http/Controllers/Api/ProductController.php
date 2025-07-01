<?php

namespace App\Http\Controllers\Api;

use App\Filters\ProductFilter;
use App\Http\ApiPagination;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use App\Traits\HandleApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    use HandleApiResponse;

    public function __construct(
        private readonly ProductService $productService,
        private readonly ApiPagination $apiPagination
    ){}

    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->apiPagination->paginate(Product::query(), ProductResource::class, new ProductFilter($request));
    }

    public function show(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $product = $this->productService->getProductById($id);
            return new ProductResource($product);
        });
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        return $this->handleResponse(function () use ($request) {
            $product = $this->productService->createProduct($request->validated());
            return ['message' => 'Product created successfully', 'data' => new ProductResource($product)];
        });
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id, $request) {
            $product = $this->productService->updateProduct($id, $request->validated());
            return ['message' => 'Product updated successfully', 'data' => new ProductResource($product)];
        });
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $this->productService->deleteProduct($id);
            return ['message' => 'Product deleted successfully'];
        });
    }
}
