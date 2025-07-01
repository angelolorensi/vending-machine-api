<?php

namespace App\Http\Controllers\Api;

use App\Filters\ClassificationFilter;
use App\Http\ApiPagination;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClassificationResource;
use App\Models\Classification;
use App\Services\ClassificationService;
use App\Traits\HandleApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreClassificationRequest;
use App\Http\Requests\UpdateClassificationRequest;

class ClassificationController extends Controller
{
    use HandleApiResponse;

    public function __construct(
        private readonly ClassificationService $classificationService,
        private readonly ApiPagination $apiPagination
    ){}

    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->apiPagination->paginate(Classification::query(), ClassificationResource::class, new ClassificationFilter($request));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($request, $id) {
            $filter = new ClassificationFilter($request);
            $classification = $this->classificationService->getClassificationById($id, $filter);
            return new ClassificationResource($classification);
        });
    }

    public function store(StoreClassificationRequest $request): JsonResponse
    {
        return $this->handleResponse(function () use ($request) {
            $classification = $this->classificationService->createClassification($request->validated());
            return ['message' => 'Classification created successfully', 'data' => new ClassificationResource($classification)];
        });
    }

    public function update(UpdateClassificationRequest $request, int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id, $request) {
            $classification = $this->classificationService->updateClassification($id, $request->validated());
            return ['message' => 'Classification updated successfully', 'data' => new ClassificationResource($classification)];
        });
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $this->classificationService->deleteClassification($id);
            return ['message' => 'Classification deleted successfully'];
        });
    }
}
