<?php

namespace App\Http\Controllers\Api;

use App\Actions\PurchaseProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Traits\HandleApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    use HandleApiResponse;

    public function purchase(PurchaseRequest $request, PurchaseProductAction $action): JsonResponse
    {
        return $this->handleResponse(function () use ($request, $action) {
            $result = $action->execute($request->card_number, $request->machine_id, $request->slot_number);
            return ['message' => 'Purchase successful', 'data' => new PurchaseResource($result)];
        });
    }
}
