<?php

namespace Tests\Unit;

use App\Services\EmployeeService;
use App\Models\Employee;
use App\Models\Classification;
use App\Models\Card;
use App\Models\Transaction;
use App\Enums\EmployeeStatus;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmployeeService $employeeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employeeService = new EmployeeService();
    }

    public function test_can_get_employee_by_id()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'name' => 'John Doe',
            'classification_id' => $classification->classification_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->employeeService->getEmployeeById($employee->employee_id);

        // Assert
        $this->assertEquals($employee->employee_id, $result->employee_id);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals(EmployeeStatus::ACTIVE, $result->status);
        $this->assertTrue($result->relationLoaded('classification'));
        $this->assertTrue($result->relationLoaded('card'));
        $this->assertTrue($result->relationLoaded('transactions'));
    }

    public function test_throws_exception_when_employee_not_found_by_id()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Employee not found');

        $this->employeeService->getEmployeeById(999);
    }

    public function test_can_create_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employeeData = [
            'name' => 'Jane Smith',
            'classification_id' => $classification->classification_id,
            'status' => EmployeeStatus::ACTIVE
        ];

        // Act
        $result = $this->employeeService->createEmployee($employeeData);

        // Assert
        $this->assertInstanceOf(Employee::class, $result);
        $this->assertEquals('Jane Smith', $result->name);
        $this->assertEquals($classification->classification_id, $result->classification_id);
        $this->assertEquals(EmployeeStatus::ACTIVE, $result->status);

        $this->assertDatabaseHas('employees', [
            'name' => 'Jane Smith',
            'classification_id' => $classification->classification_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
    }

    public function test_can_create_employee_with_card()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create();
        $employeeData = [
            'name' => 'Bob Johnson',
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ];

        // Act
        $result = $this->employeeService->createEmployee($employeeData);

        // Assert
        $this->assertEquals($card->card_id, $result->card_id);
        $this->assertDatabaseHas('employees', [
            'name' => 'Bob Johnson',
            'card_id' => $card->card_id
        ]);
    }

    public function test_can_update_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'name' => 'Original Name',
            'classification_id' => $classification->classification_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'status' => EmployeeStatus::INACTIVE
        ];

        // Act
        $result = $this->employeeService->updateEmployee($employee->employee_id, $updateData);

        // Assert
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals(EmployeeStatus::INACTIVE, $result->status);

        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'name' => 'Updated Name',
            'status' => EmployeeStatus::INACTIVE
        ]);
    }

    public function test_throws_exception_when_updating_nonexistent_employee()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Employee not found');

        $this->employeeService->updateEmployee(999, ['name' => 'Updated']);
    }

    public function test_can_delete_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id
        ]);

        // Act
        $result = $this->employeeService->deleteEmployee($employee->employee_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('employees', [
            'employee_id' => $employee->employee_id
        ]);
    }

    public function test_throws_exception_when_deleting_nonexistent_employee()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Employee not found');

        $this->employeeService->deleteEmployee(999);
    }

    public function test_get_employee_by_id_loads_all_relationships()
    {
        // Arrange
        $classification = Classification::factory()->create(['name' => 'Manager']);
        $card = Card::factory()->create(['card_number' => 'TEST123']);
        $employee = Employee::factory()->create([
            'name' => 'Test Employee',
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id
        ]);
        $transaction = Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'card_id' => $card->card_id
        ]);

        // Act
        $result = $this->employeeService->getEmployeeById($employee->employee_id);

        // Assert
        $this->assertNotNull($result->classification);
        $this->assertEquals('Manager', $result->classification->name);

        $this->assertNotNull($result->card);
        $this->assertEquals('TEST123', $result->card->card_number);

        $this->assertCount(1, $result->transactions);
        $this->assertEquals($transaction->transaction_id, $result->transactions->first()->transaction_id);
    }

    public function test_update_returns_fresh_instance()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'name' => 'Original Name',
            'classification_id' => $classification->classification_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->employeeService->updateEmployee(
            $employee->employee_id,
            ['name' => 'Updated Name', 'status' => EmployeeStatus::INACTIVE]
        );

        // Assert
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals(EmployeeStatus::INACTIVE, $result->status);

        // Verify original instance wasn't modified
        $this->assertEquals('Original Name', $employee->name);
        $this->assertEquals(EmployeeStatus::ACTIVE, $employee->status);

        // Verify database was updated
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'name' => 'Updated Name',
            'status' => EmployeeStatus::INACTIVE
        ]);
    }

    public function test_can_update_employee_card_assignment()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $oldCard = Card::factory()->create();
        $newCard = Card::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $oldCard->card_id
        ]);

        // Act
        $result = $this->employeeService->updateEmployee($employee->employee_id, [
            'card_id' => $newCard->card_id
        ]);

        // Assert
        $this->assertEquals($newCard->card_id, $result->card_id);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => $newCard->card_id
        ]);
    }

    public function test_can_remove_card_from_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id
        ]);

        // Act
        $result = $this->employeeService->updateEmployee($employee->employee_id, [
            'card_id' => null
        ]);

        // Assert
        $this->assertNull($result->card_id);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => null
        ]);
    }
}
