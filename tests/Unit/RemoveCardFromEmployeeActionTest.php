<?php

namespace Tests\Unit;

use App\Actions\RemoveCardFromEmployeeAction;
use App\Services\EmployeeService;
use App\Models\Card;
use App\Models\Employee;
use App\Models\Classification;
use App\Enums\CardStatus;
use App\Enums\EmployeeStatus;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveCardFromEmployeeActionTest extends TestCase
{
    use RefreshDatabase;

    private RemoveCardFromEmployeeAction $removeCardFromEmployeeAction;
    private EmployeeService $employeeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employeeService = new EmployeeService();
        
        $this->removeCardFromEmployeeAction = new RemoveCardFromEmployeeAction($this->employeeService);
    }

    public function test_can_successfully_remove_card_from_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->removeCardFromEmployeeAction->execute($employee->employee_id);

        // Assert
        $this->assertTrue($result);
        
        // Verify employee's card_id was set to null
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => null
        ]);

        // Verify the employee model is updated
        $employee->refresh();
        $this->assertNull($employee->card_id);
    }

    public function test_throws_exception_when_employee_not_found()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Employee not found');

        // Act
        $this->removeCardFromEmployeeAction->execute(999);
    }

    public function test_throws_exception_when_employee_has_no_card()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => null,
            'status' => EmployeeStatus::ACTIVE
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Employee does not have a card assigned');

        // Act
        $this->removeCardFromEmployeeAction->execute($employee->employee_id);
    }

    public function test_can_remove_card_from_inactive_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::INACTIVE
        ]);

        // Act
        $result = $this->removeCardFromEmployeeAction->execute($employee->employee_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => null
        ]);
    }

    public function test_can_remove_blocked_card_from_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $blockedCard = Card::factory()->create([
            'status' => CardStatus::BLOCKED
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $blockedCard->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->removeCardFromEmployeeAction->execute($employee->employee_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => null
        ]);
    }

    public function test_can_remove_inactive_card_from_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $inactiveCard = Card::factory()->create([
            'status' => CardStatus::INACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $inactiveCard->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->removeCardFromEmployeeAction->execute($employee->employee_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => null
        ]);
    }

    public function test_card_remains_in_database_after_removal_from_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'REMAIN123',
            'status' => CardStatus::ACTIVE,
            'points_balance' => 100
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $this->removeCardFromEmployeeAction->execute($employee->employee_id);

        // Assert - card should still exist in database
        $this->assertDatabaseHas('cards', [
            'card_id' => $card->card_id,
            'card_number' => 'REMAIN123',
            'status' => CardStatus::ACTIVE->value,
            'points_balance' => 100
        ]);

        // Assert - employee should no longer have the card
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => null
        ]);
    }

    public function test_database_remains_unchanged_when_employee_has_no_card()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'name' => 'No Card Employee',
            'classification_id' => $classification->classification_id,
            'card_id' => null,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Store original state
        $originalEmployee = $employee->toArray();

        try {
            // Act
            $this->removeCardFromEmployeeAction->execute($employee->employee_id);
        } catch (\Exception $e) {
            // Expected exception
        }

        // Assert - employee should remain unchanged
        $employee->refresh();
        $this->assertEquals($originalEmployee['name'], $employee->name);
        $this->assertNull($employee->card_id);
        $this->assertEquals($originalEmployee['status'], $employee->status->value);
    }

    public function test_multiple_employees_can_have_their_cards_removed()
    {
        // Arrange
        $classification = Classification::factory()->create();
        
        $card1 = Card::factory()->create();
        $employee1 = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card1->card_id
        ]);
        
        $card2 = Card::factory()->create();
        $employee2 = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card2->card_id
        ]);

        // Act
        $result1 = $this->removeCardFromEmployeeAction->execute($employee1->employee_id);
        $result2 = $this->removeCardFromEmployeeAction->execute($employee2->employee_id);

        // Assert
        $this->assertTrue($result1);
        $this->assertTrue($result2);
        
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee1->employee_id,
            'card_id' => null
        ]);
        
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee2->employee_id,
            'card_id' => null
        ]);
    }

    public function test_removes_card_from_employee_with_zero_balance()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'points_balance' => 0,
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->removeCardFromEmployeeAction->execute($employee->employee_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => null
        ]);

        // Card should still exist with zero balance
        $this->assertDatabaseHas('cards', [
            'card_id' => $card->card_id,
            'points_balance' => 0
        ]);
    }

    public function test_removes_card_from_employee_with_high_balance()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'points_balance' => 1000,
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->removeCardFromEmployeeAction->execute($employee->employee_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => null
        ]);

        // Card should still exist with high balance
        $this->assertDatabaseHas('cards', [
            'card_id' => $card->card_id,
            'points_balance' => 1000
        ]);
    }
}