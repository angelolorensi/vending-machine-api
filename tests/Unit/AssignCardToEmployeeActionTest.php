<?php

namespace Tests\Unit;

use App\Actions\AssignCardToEmployeeAction;
use App\Services\CardService;
use App\Services\EmployeeService;
use App\Models\Card;
use App\Models\Employee;
use App\Models\Classification;
use App\Enums\CardStatus;
use App\Enums\EmployeeStatus;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignCardToEmployeeActionTest extends TestCase
{
    use RefreshDatabase;

    private AssignCardToEmployeeAction $assignCardToEmployeeAction;
    private CardService $cardService;
    private EmployeeService $employeeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cardService = new CardService();
        $this->employeeService = new EmployeeService();

        $this->assignCardToEmployeeAction = new AssignCardToEmployeeAction(
            $this->cardService,
            $this->employeeService
        );
    }

    public function test_can_successfully_assign_card_to_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => null,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $card = Card::factory()->create([
            'status' => CardStatus::ACTIVE
        ]);

        // Act
        $result = $this->assignCardToEmployeeAction->execute($card->card_id, $employee->employee_id);

        // Assert
        $this->assertTrue($result);

        // Verify employee was updated with card_id
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => $card->card_id
        ]);

        // Verify the employee model is updated
        $employee->refresh();
        $this->assertEquals($card->card_id, $employee->card_id);
    }

    public function test_throws_exception_when_employee_not_found()
    {
        // Arrange
        $card = Card::factory()->create();

        // Assert
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Employee not found');

        // Act
        $this->assignCardToEmployeeAction->execute($card->card_id, 999);
    }

    public function test_throws_exception_when_card_not_found()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => null
        ]);

        // Assert
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Card not found');

        // Act
        $this->assignCardToEmployeeAction->execute(999, $employee->employee_id);
    }

    public function test_throws_exception_when_employee_already_has_card()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $existingCard = Card::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $existingCard->card_id
        ]);
        $newCard = Card::factory()->create();

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Employee already has a card assigned');

        // Act
        $this->assignCardToEmployeeAction->execute($newCard->card_id, $employee->employee_id);
    }

    public function test_throws_exception_when_card_already_assigned_to_another_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();

        // First employee with the card
        $firstEmployee = Employee::factory()->create([
            'classification_id' => $classification->classification_id
        ]);
        $card = Card::factory()->create();

        // Assign card to first employee
        $firstEmployee->update(['card_id' => $card->card_id]);

        // Second employee without a card
        $secondEmployee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => null
        ]);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Card is already assigned to another employee');

        // Act - try to assign the same card to second employee
        $this->assignCardToEmployeeAction->execute($card->card_id, $secondEmployee->employee_id);
    }

    public function test_assigns_card_with_different_statuses()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => null
        ]);
        $inactiveCard = Card::factory()->create([
            'status' => CardStatus::INACTIVE
        ]);

        // Act
        $result = $this->assignCardToEmployeeAction->execute($inactiveCard->card_id, $employee->employee_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => $inactiveCard->card_id
        ]);
    }

    public function test_assigns_card_to_inactive_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => null,
            'status' => EmployeeStatus::INACTIVE
        ]);
        $card = Card::factory()->create([
            'status' => CardStatus::ACTIVE
        ]);

        // Act
        $result = $this->assignCardToEmployeeAction->execute($card->card_id, $employee->employee_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => $card->card_id
        ]);
    }

    public function test_can_assign_blocked_card_to_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => null
        ]);
        $blockedCard = Card::factory()->create([
            'status' => CardStatus::BLOCKED
        ]);

        // Act
        $result = $this->assignCardToEmployeeAction->execute($blockedCard->card_id, $employee->employee_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => $blockedCard->card_id
        ]);
    }

    public function test_database_remains_unchanged_when_employee_already_has_card()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $existingCard = Card::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $existingCard->card_id
        ]);
        $newCard = Card::factory()->create();

        try {
            // Act
            $this->assignCardToEmployeeAction->execute($newCard->card_id, $employee->employee_id);
        } catch (\Exception $e) {
            // Expected exception
        }

        // Assert - employee should still have the original card
        $this->assertDatabaseHas('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => $existingCard->card_id
        ]);

        // Assert - new card should not be assigned to the employee
        $this->assertDatabaseMissing('employees', [
            'employee_id' => $employee->employee_id,
            'card_id' => $newCard->card_id
        ]);
    }

    public function test_database_remains_unchanged_when_card_already_assigned()
    {
        // Arrange
        $classification = Classification::factory()->create();

        $firstEmployee = Employee::factory()->create([
            'classification_id' => $classification->classification_id
        ]);
        $card = Card::factory()->create();
        $firstEmployee->update(['card_id' => $card->card_id]);

        $secondEmployee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => null
        ]);

        try {
            // Act
            $this->assignCardToEmployeeAction->execute($card->card_id, $secondEmployee->employee_id);
        } catch (\Exception $e) {
            // Expected exception
        }

        // Assert - first employee should still have the card
        $this->assertDatabaseHas('employees', [
            'employee_id' => $firstEmployee->employee_id,
            'card_id' => $card->card_id
        ]);

        // Assert - second employee should not have the card
        $this->assertDatabaseHas('employees', [
            'employee_id' => $secondEmployee->employee_id,
            'card_id' => null
        ]);
    }
}
