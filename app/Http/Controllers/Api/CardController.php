<?php

namespace App\Http\Controllers\Api;

use App\Actions\AssignCardToEmployeeAction;
use App\Actions\RemoveCardFromEmployeeAction;
use App\Actions\VerifyCardAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CardVerificationRequest;
use App\Http\Resources\CardResource;
use App\Http\Resources\CardVerificationResource;
use App\Services\CardService;
use App\Traits\HandleApiResponse;
use Illuminate\Http\JsonResponse;
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

    public function store(StoreCardRequest $request): JsonResponse
    {
        return $this->handleResponse(function () use ($request) {
            $card = $this->cardService->createCard($request->validated());
            return ['message' => 'Card created successfully', 'data' => new CardResource($card)];
        });
    }

    public function update(int $id, UpdateCardRequest $request): JsonResponse
    {
        return $this->handleResponse(function () use ($id, $request) {
            $card = $this->cardService->updateCard($id, $request->validated());
            return ['message' => 'Card updated successfully', 'data' => new CardResource($card)];
        });
    }

    public function verifyCard(CardVerificationRequest $request, VerifyCardAction $action): JsonResponse
    {
        return $this->handleResponse(function () use ($request, $action) {
            $result = $action->execute($request->input('card_number'));
            return ['message' => 'Card verification successful', 'data' => new CardVerificationResource($result)];
        });
    }

    public function assignCardToEmployee(int $cardId, int $employeeId, AssignCardToEmployeeAction $action): JsonResponse
    {
        return $this->handleResponse(function () use ($cardId, $employeeId, $action) {
            $action->execute($cardId, $employeeId);
            return ['message' => 'Card assigned to employee successfully'];
        });
    }

    public function removeFromEmployee(int $employeeId, RemoveCardFromEmployeeAction $action): JsonResponse
    {
        return $this->handleResponse(function () use ($employeeId, $action) {
            $action->execute($employeeId);
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
