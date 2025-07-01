<?php

namespace App\Http\Controllers\Api;

use App\Filters\CardFilter;
use App\Http\ApiPagination;
use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Services\CardService;
use App\Traits\HandleApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;

class CardController extends Controller
{
    use HandleApiResponse;

    public function __construct(
        private readonly CardService $cardService,

    ){}

    public function show(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $card = $this->cardService->getCardById($id);
            return new CardResource($card);
        });
    }

    public function store(): JsonResponse
    {
        return $this->handleResponse(function (StoreCardRequest $request) {
            $card = $this->cardService->createCard($request->validated());
            return ['message' => 'Card created successfully', 'data' => new CardResource($card)];
        });
    }

    public function update(int $id): JsonResponse
    {
        return $this->handleResponse(function ($id, UpdateCardRequest $request) {
            $card = $this->cardService->updateCard($id,$request->validated());
            return ['message' => 'Card updated successfully', 'data' => new CardResource($card)];
        });
    }

    public function assignCardToEmployee(int $cardId, int $employeeId): JsonResponse
    {
        return $this->handleResponse(function () use ($cardId, $employeeId) {
            $card = $this->cardService->assignCardToEmployee($cardId, $employeeId);
            return ['message' => 'Card created and assigned to employee successfully', 'data' => new CardResource($card)];
        });
    }

    public function removeFromEmployee(int $employeeId): JsonResponse
    {
        return $this->handleResponse(function () use ($employeeId) {
            $this->cardService->removeFromEmployee($employeeId);
            return ['message' => 'Card removed from employee successfully'];
        });
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $this->cardService->deleteCard($id);
            return ['message' => 'Card deleted successfully'];
        });
    }
}
