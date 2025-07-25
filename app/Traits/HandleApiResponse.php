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
                'message' => $result['message'] ?? 'Operation successful',
                'data' => $result['data'],
            ]);
        } catch (Throwable $e) {
            $errorResponse = [
                'success' => false,
                'message' => 'Internal server error',
            ];

            if(!app()->isProduction()){
                $errorResponse['exception'] = get_class($e);
                $errorResponse['error'] = $e->getMessage();
            }

            return response()->json($errorResponse, 500);
        }
    }
}
