<?php

namespace App\Http\Controllers\Api;

use App\Filters\MachineFilter;
use App\Http\ApiPagination;
use App\Http\Controllers\Controller;
use App\Http\Resources\MachineResource;
use App\Models\Machine;
use App\Services\MachineService;
use App\Traits\HandleApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MachineController extends Controller
{
    use HandleApiResponse;

    public function __construct(
        private readonly MachineService $machineService,
        private readonly ApiPagination  $apiPagination
    ){}

    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->apiPagination->paginate(Machine::query(), MachineResource::class, new MachineFilter($request));
    }

    public function show(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            return $this->machineService->getMachineById($id);
        });
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:active,inactive'
        ]);

        return $this->handleResponse(function () use ($id, $request) {
            $machine = $this->machineService->updateMachine($id, $request->all());
            return ['message' => 'Machine updated successfully', 'data' => $machine];
        });
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $this->machineService->deleteMachine($id);
            return ['message' => 'Machine deleted successfully'];
        });
    }
}
