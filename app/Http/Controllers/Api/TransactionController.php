<?php

namespace App\Http\Controllers\Api;

use App\Http\ApiPagination;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Traits\HandleApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends Controller
{
    use HandleApiResponse;

    public function __construct(
        private readonly TransactionService $transactionService,
        private readonly ApiPagination $apiPagination
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return $this->apiPagination->paginate(Transaction::query(), TransactionResource::class);
    }

    public function show(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $transaction = $this->transactionService->getTransactionById($id);
            return ['data' => new TransactionResource($transaction)];
        });
    }
}
