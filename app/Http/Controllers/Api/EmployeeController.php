<?php

namespace App\Http\Controllers\Api;

use App\Filters\EmployeeFilter;
use App\Http\ApiPagination;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Traits\HandleApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    use HandleApiResponse;

    public function __construct(
        private readonly EmployeeService $employeeService,
        private readonly ApiPagination $apiPagination
    ){}

    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->apiPagination->paginate(Employee::query(), EmployeeResource::class, new EmployeeFilter($request));
    }

    public function show(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $employee = $this->employeeService->getEmployeeById($id);
            return ['data' => new EmployeeResource($employee)];
        });
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        return $this->handleResponse(function () use ($request) {
            $employee = $this->employeeService->createEmployee($request->validated());
            return ['message' => 'Employee created successfully', 'data' => new EmployeeResource($employee)];
        });
    }

    public function update(UpdateEmployeeRequest $request, int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id, $request) {
            $employee = $this->employeeService->updateEmployee($id, $request->validated());
            return ['message' => 'Employee updated successfully', 'data' => new EmployeeResource($employee)];
        });
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->handleResponse(function () use ($id) {
            $this->employeeService->deleteEmployee($id);
            return ['message' => 'Employee deleted successfully'];
        });
    }
}
