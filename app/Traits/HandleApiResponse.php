<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Throwable;

trait HandleApiResponse
{
    protected function handleResponse(callable $callback): JsonResponse
    {
        try {
            $result = $callback();

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
