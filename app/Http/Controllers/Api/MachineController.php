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
use App\Http\Requests\StoreMachineRequest;
use App\Http\Requests\UpdateMachineRequest;

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
            $machine = $this->machineService->getMachineById($id);
            return ['data' => new MachineResource($machine)];
        });
    }

    public function store(StoreMachineRequest $request): JsonResponse
    {
        return $this->handleResponse(function () use ($request) {
            $machine = $this->machineService->createMachine($request->validated());
            return ['message' => 'Machine created successfully', 'data' => new MachineResource($machine)];
        });
    }

    public function update(UpdateMachineRequest $request, int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id, $request) {
            $machine = $this->machineService->updateMachine($id, $request->validated());
            return ['message' => 'Machine updated successfully', 'data' => new MachineResource($machine)];
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
