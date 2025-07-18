<?php

namespace Feature;

use App\Enums\CardStatus;
use App\Exceptions\NotFoundException;
use App\Models\Card;
use App\Models\Classification;
use App\Models\Employee;
use App\Models\Transaction;
use App\Services\CardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardServiceTests extends TestCase
{
    use RefreshDatabase;

    private CardService $cardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cardService = new CardService();
    }

    public function test_can_get_card_by_id()
    {
        // Arrange
        $card = Card::factory()->create();

        // Act
        $result = $this->cardService->getCardById($card->card_id);

        // Assert
        $this->assertEquals($card->card_id, $result->card_id);
        $this->assertEquals($card->card_number, $result->card_number);
        $this->assertTrue($result->relationLoaded('employee'));
        $this->assertTrue($result->relationLoaded('transactions'));
    }

    public function test_throws_exception_when_card_not_found_by_id()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Card not found');

        $this->cardService->getCardById(999);
    }

    public function test_can_get_card_by_number()
    {
        // Arrange
        $card = Card::factory()->create(['card_number' => 'TEST123']);

        // Act
        $result = $this->cardService->getCardByNumber('TEST123');

        // Assert
        $this->assertEquals($card->card_id, $result->card_id);
        $this->assertEquals('TEST123', $result->card_number);
        $this->assertTrue($result->relationLoaded('employee'));
    }

    public function test_throws_exception_when_card_not_found_by_number()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Card not found');

        $this->cardService->getCardByNumber('NONEXISTENT');
    }

    public function test_can_create_card()
    {
        // Arrange
        $cardData = [
            'card_number' => 'NEW123',
            'points_balance' => 100,
            'status' => 'active'
        ];

        // Act
        $result = $this->cardService->createCard($cardData);

        // Assert
        $this->assertInstanceOf(Card::class, $result);
        $this->assertEquals('NEW123', $result->card_number);
        $this->assertEquals(100, $result->points_balance);
        $this->assertEquals(CardStatus::ACTIVE, $result->status);

        $this->assertDatabaseHas('cards', [
            'card_number' => 'NEW123',
            'points_balance' => 100,
            'status' => 'active'
        ]);
    }

    public function test_can_update_card()
    {
        // Arrange
        $card = Card::factory()->create([
            'card_number' => 'OLD123',
            'points_balance' => 50
        ]);

        $updateData = [
            'card_number' => 'UPDATED123',
            'points_balance' => 75
        ];

        // Act
        $result = $this->cardService->updateCard($card->card_id, $updateData);

        // Assert
        $this->assertEquals('UPDATED123', $result->card_number);
        $this->assertEquals(75, $result->points_balance);

        $this->assertDatabaseHas('cards', [
            'card_id' => $card->card_id,
            'card_number' => 'UPDATED123',
            'points_balance' => 75
        ]);
    }

    public function test_throws_exception_when_updating_nonexistent_card()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Card not found');

        $this->cardService->updateCard(999, ['points_balance' => 100]);
    }

    public function test_can_delete_card()
    {
        // Arrange
        $card = Card::factory()->create();

        // Act
        $result = $this->cardService->deleteCard($card->card_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('cards', [
            'card_id' => $card->card_id
        ]);
    }

    public function test_throws_exception_when_deleting_nonexistent_card()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Card not found');

        $this->cardService->deleteCard(999);
    }

    public function test_get_card_by_id_loads_relationships()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id
        ]);
        $transaction = Transaction::factory()->create([
            'card_id' => $card->card_id,
            'employee_id' => $employee->employee_id
        ]);

        // Act
        $result = $this->cardService->getCardById($card->card_id);

        // Assert
        $this->assertNotNull($result->employee);
        $this->assertEquals($employee->employee_id, $result->employee->employee_id);
        $this->assertCount(1, $result->transactions);
        $this->assertEquals($transaction->transaction_id, $result->transactions->first()->transaction_id);
    }

    public function test_get_card_by_number_loads_employee_relationship()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create(['card_number' => 'REL123']);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id
        ]);

        // Act
        $result = $this->cardService->getCardByNumber('REL123');

        // Assert
        $this->assertNotNull($result->employee);
        $this->assertEquals($employee->employee_id, $result->employee->employee_id);
    }

    public function test_update_returns_fresh_instance()
    {
        // Arrange
        $card = Card::factory()->create(['points_balance' => 50]);

        // Act
        $result = $this->cardService->updateCard($card->card_id, ['points_balance' => 100]);

        // Assert
        $this->assertEquals(100, $result->points_balance);

        // Verify original instance wasn't modified
        $this->assertEquals(50, $card->points_balance);

        // Verify database was updated
        $this->assertDatabaseHas('cards', [
            'card_id' => $card->card_id,
            'points_balance' => 100
        ]);
    }
}
