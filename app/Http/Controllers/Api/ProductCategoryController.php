<?php

namespace App\Http\Controllers\Api;

use App\Filters\ProductCategoryFilter;
use App\Http\ApiPagination;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Services\ProductCategoryService;
use App\Traits\HandleApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;

class ProductCategoryController extends Controller
{
    use HandleApiResponse;

    public function __construct(
        private readonly ProductCategoryService $productCategoryService,
        private readonly ApiPagination $apiPagination
    ){}

    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->apiPagination->paginate(ProductCategory::query(), ProductCategoryResource::class, new ProductCategoryFilter($request));
    }

    public function show(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $productCategory = $this->productCategoryService->getProductCategoryById($id);
            return new ProductCategoryResource($productCategory);
        });
    }

    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        return $this->handleResponse(function () use ($request) {
            $productCategory = $this->productCategoryService->createProductCategory($request->validated());
            return ['message' => 'Product category created successfully', 'data' => new ProductCategoryResource($productCategory)];
        });
    }

    public function update(UpdateProductCategoryRequest $request, int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id, $request) {
            $productCategory = $this->productCategoryService->updateProductCategory($id, $request->validated());
            return ['message' => 'Product category updated successfully', 'data' => new ProductCategoryResource($productCategory)];
        });
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $this->productCategoryService->deleteProductCategory($id);
            return ['message' => 'Product category deleted successfully'];
        });
    }
}
