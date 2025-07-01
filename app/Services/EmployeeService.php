<?php

namespace App\Services;

use App\Models\Employee;
use App\Exceptions\NotFoundException;

class EmployeeService
{
    public function getEmployeeById(int $id): Employee
    {
        $employee = Employee::with(['classification', 'card', 'transactions'])->find($id);

        if (!$employee) {
            throw new NotFoundException('Employee not found');
        }

        return $employee;
    }

    public function createEmployee(array $data): Employee
    {
        return Employee::create($data);
    }

    public function updateEmployee(int $id, array $data): Employee
    {
        $employee = Employee::find($id);

        if (!$employee) {
            throw new NotFoundException('Employee not found');
        }

        $employee->update($data);
        return $employee->fresh();
    }

    public function deleteEmployee(int $id): bool
    {
        $employee = Employee::find($id);

        if (!$employee) {
            throw new NotFoundException('Employee not found');
        }

        return $employee->delete();
    }
}
