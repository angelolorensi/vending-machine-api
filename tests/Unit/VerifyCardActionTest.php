<?php

namespace Tests\Unit;

use App\Actions\VerifyCardAction;
use App\Services\CardService;
use App\Models\Card;
use App\Models\Employee;
use App\Models\Classification;
use App\Enums\CardStatus;
use App\Enums\EmployeeStatus;
use App\Exceptions\NotFoundException;
use App\Exceptions\BlockedCardException;
use App\Exceptions\NotActiveException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifyCardActionTest extends TestCase
{
    use RefreshDatabase;

    private VerifyCardAction $verifyCardAction;
    private CardService $cardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cardService = new CardService();
        
        $this->verifyCardAction = new VerifyCardAction($this->cardService);
    }

    public function test_can_successfully_verify_active_card_with_active_employee()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'VALID123',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->verifyCardAction->execute('VALID123');

        // Assert
        $this->assertInstanceOf(Card::class, $result);
        $this->assertEquals($card->card_id, $result->card_id);
        $this->assertEquals('VALID123', $result->card_number);
        $this->assertEquals(CardStatus::ACTIVE, $result->status);
        
        // Verify employee relationship is loaded
        $this->assertNotNull($result->employee);
        $this->assertEquals($employee->employee_id, $result->employee->employee_id);
        $this->assertEquals(EmployeeStatus::ACTIVE, $result->employee->status);
    }

    public function test_throws_exception_when_card_not_found()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Card not found');

        // Act
        $this->verifyCardAction->execute('NONEXISTENT123');
    }

    public function test_throws_exception_when_card_is_blocked()
    {
        // Arrange
        $card = Card::factory()->create([
            'card_number' => 'BLOCKED123',
            'status' => CardStatus::BLOCKED
        ]);

        $this->expectException(BlockedCardException::class);

        // Act
        $this->verifyCardAction->execute('BLOCKED123');
    }

    public function test_throws_exception_when_card_is_inactive()
    {
        // Arrange
        $card = Card::factory()->create([
            'card_number' => 'INACTIVE123',
            'status' => CardStatus::INACTIVE
        ]);

        $this->expectException(NotActiveException::class);
        $this->expectExceptionMessage('Card is not active');

        // Act
        $this->verifyCardAction->execute('INACTIVE123');
    }

    public function test_throws_exception_when_card_has_no_employee()
    {
        // Arrange
        $card = Card::factory()->create([
            'card_number' => 'UNASSIGNED123',
            'status' => CardStatus::ACTIVE
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Card is not assigned to any employee');

        // Act
        $this->verifyCardAction->execute('UNASSIGNED123');
    }

    public function test_throws_exception_when_employee_is_inactive()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'INACTIVE_EMP123',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::INACTIVE
        ]);

        $this->expectException(NotActiveException::class);
        $this->expectExceptionMessage('Employee is not active');

        // Act
        $this->verifyCardAction->execute('INACTIVE_EMP123');
    }

    public function test_verifies_card_with_different_number_formats()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => '1234567890',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->verifyCardAction->execute('1234567890');

        // Assert
        $this->assertEquals('1234567890', $result->card_number);
        $this->assertEquals(CardStatus::ACTIVE, $result->status);
    }

    public function test_verifies_card_with_alphanumeric_number()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'ABC123XYZ',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->verifyCardAction->execute('ABC123XYZ');

        // Assert
        $this->assertEquals('ABC123XYZ', $result->card_number);
        $this->assertEquals(CardStatus::ACTIVE, $result->status);
    }

    public function test_card_verification_loads_employee_with_classification()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'name' => 'Manager',
            'daily_point_limit' => 100
        ]);
        $card = Card::factory()->create([
            'card_number' => 'MANAGER123',
            'status' => CardStatus::ACTIVE,
            'points_balance' => 50
        ]);
        $employee = Employee::factory()->create([
            'name' => 'John Manager',
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->verifyCardAction->execute('MANAGER123');

        // Assert
        $this->assertEquals('MANAGER123', $result->card_number);
        $this->assertEquals(50, $result->points_balance);
        
        // Verify employee is loaded
        $this->assertNotNull($result->employee);
        $this->assertEquals('John Manager', $result->employee->name);
        
        // Verify classification is loaded through employee
        $this->assertTrue($result->employee->relationLoaded('classification'));
        $this->assertEquals('Manager', $result->employee->classification->name);
        $this->assertEquals(100, $result->employee->classification->daily_point_limit);
    }

    public function test_blocked_card_exception_takes_precedence_over_unassigned_card()
    {
        // Arrange
        $card = Card::factory()->create([
            'card_number' => 'BLOCKED_UNASSIGNED',
            'status' => CardStatus::BLOCKED
        ]);

        // Should throw BlockedCardException, not the unassigned employee exception
        $this->expectException(BlockedCardException::class);

        // Act
        $this->verifyCardAction->execute('BLOCKED_UNASSIGNED');
    }

    public function test_inactive_card_exception_takes_precedence_over_unassigned_card()
    {
        // Arrange
        $card = Card::factory()->create([
            'card_number' => 'INACTIVE_UNASSIGNED',
            'status' => CardStatus::INACTIVE
        ]);

        // Should throw NotActiveException for card, not the unassigned employee exception
        $this->expectException(NotActiveException::class);
        $this->expectExceptionMessage('Card is not active');

        // Act
        $this->verifyCardAction->execute('INACTIVE_UNASSIGNED');
    }

    public function test_unassigned_card_exception_takes_precedence_over_inactive_employee()
    {
        // Arrange
        $card = Card::factory()->create([
            'card_number' => 'UNASSIGNED_CARD',
            'status' => CardStatus::ACTIVE
        ]);

        // Should throw unassigned card exception, not inactive employee
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Card is not assigned to any employee');

        // Act
        $this->verifyCardAction->execute('UNASSIGNED_CARD');
    }

    public function test_card_verification_handles_case_sensitive_card_numbers()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'CaseSensitive123',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Act
        $result = $this->verifyCardAction->execute('CaseSensitive123');

        // Assert
        $this->assertEquals('CaseSensitive123', $result->card_number);

        // Different case should not find the card
        $this->expectException(NotFoundException::class);
        $this->verifyCardAction->execute('casesensitive123');
    }
}