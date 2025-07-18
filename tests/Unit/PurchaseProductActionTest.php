<?php

namespace Tests\Unit;

use App\Actions\PurchaseProductAction;
use App\Services\CardService;
use App\Services\MachineService;
use App\Services\SlotService;
use App\Services\TransactionService;
use App\Models\Card;
use App\Models\Employee;
use App\Models\Machine;
use App\Models\Slot;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Classification;
use App\Models\Transaction;
use App\Enums\CardStatus;
use App\Enums\EmployeeStatus;
use App\Enums\MachineStatus;
use App\Enums\TransactionStatus;
use App\Exceptions\NotFoundException;
use App\Exceptions\NotActiveException;
use App\Exceptions\InsufficientPointsException;
use App\Exceptions\DailyLimitExceededException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class PurchaseProductActionTest extends TestCase
{
    use RefreshDatabase;

    private PurchaseProductAction $purchaseProductAction;
    private CardService $cardService;
    private MachineService $machineService;
    private SlotService $slotService;
    private TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cardService = new CardService();
        $this->machineService = new MachineService();
        $this->slotService = new SlotService();
        $this->transactionService = new TransactionService();

        $this->purchaseProductAction = new PurchaseProductAction(
            $this->cardService,
            $this->machineService,
            $this->slotService,
            $this->transactionService
        );
    }

    public function test_can_successfully_purchase_product()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'daily_juice_limit' => 5,
            'daily_point_limit' => 100
        ]);
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'points_balance' => 50,
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::ACTIVE
        ]);
        $category = ProductCategory::factory()->create(['name' => 'beverages']);
        $product = Product::factory()->create([
            'name' => 'Coca Cola',
            'price_points' => 10,
            'product_category_id' => $category->product_category_id
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 5
        ]);

        // Act
        $result = $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 5);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('product', $result);
        $this->assertArrayHasKey('remaining_balance', $result);
        $this->assertArrayHasKey('transaction_id', $result);

        $this->assertEquals('Coca Cola', $result['product']['name']);
        $this->assertEquals(10, $result['product']['points_deducted']);
        $this->assertEquals(40, $result['remaining_balance']);

        // Verify transaction was created
        $this->assertDatabaseHas('transactions', [
            'employee_id' => $employee->employee_id,
            'card_id' => $card->card_id,
            'machine_id' => $machine->machine_id,
            'slot_id' => $slot->slot_id,
            'product_id' => $product->product_id,
            'points_deducted' => 10,
            'status' => TransactionStatus::COMPLETED->value
        ]);

        // Verify card balance was updated
        $this->assertDatabaseHas('cards', [
            'card_id' => $card->card_id,
            'points_balance' => 40
        ]);
    }

    public function test_throws_exception_when_card_not_found()
    {
        // Arrange
        $machine = Machine::factory()->create();

        // Assert
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Card not found');

        // Act
        $this->purchaseProductAction->execute('NONEXISTENT', $machine->machine_id, 1);
    }

    public function test_throws_exception_when_card_is_not_active()
    {
        // Arrange
        $card = Card::factory()->create([
            'card_number' => 'INACTIVE123',
            'status' => CardStatus::INACTIVE
        ]);
        $machine = Machine::factory()->create();

        // Assert
        $this->expectException(NotActiveException::class);
        $this->expectExceptionMessage('Card is not active');

        // Act
        $this->purchaseProductAction->execute('INACTIVE123', $machine->machine_id, 1);
    }

    public function test_throws_exception_when_employee_is_not_active()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::INACTIVE
        ]);
        $machine = Machine::factory()->create();

        // Assert
        $this->expectException(NotActiveException::class);
        $this->expectExceptionMessage('Employee is not active');

        // Act
        $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 1);
    }

    public function test_throws_exception_when_machine_not_found()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);

        // Assert
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Machine not found');

        // Act
        $this->purchaseProductAction->execute('TEST123', 999, 1);
    }

    public function test_throws_exception_when_machine_is_not_active()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::INACTIVE
        ]);

        // Assert
        $this->expectException(NotActiveException::class);
        $this->expectExceptionMessage('Machine is not active');

        // Act
        $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 1);
    }

    public function test_throws_exception_when_slot_not_found()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::ACTIVE
        ]);

        // Assert
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Slot not found');

        // Act
        $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 999);
    }

    public function test_throws_exception_when_slot_has_no_product()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::ACTIVE
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => null,
            'number' => 5
        ]);

        // Assert
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('No product in this slot');

        // Act
        $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 5);
    }

    public function test_throws_exception_when_insufficient_points()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'points_balance' => 5,
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::ACTIVE
        ]);
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'price_points' => 20,
            'product_category_id' => $category->product_category_id
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 5
        ]);

        // Assert
        $this->expectException(InsufficientPointsException::class);
        $this->expectExceptionMessage('Not enough points for this product');

        // Act
        $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 5);
    }

    public function test_throws_exception_when_daily_juice_limit_exceeded()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'daily_juice_limit' => 1,
            'daily_point_limit' => 100
        ]);
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'points_balance' => 50,
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::ACTIVE
        ]);
        $category = ProductCategory::factory()->create(['name' => 'beverages']);
        $product = Product::factory()->create([
            'price_points' => 10,
            'product_category_id' => $category->product_category_id
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 5
        ]);

        // Create existing transaction for today
        Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'product_id' => $product->product_id,
            'transaction_time' => Carbon::today(),
            'status' => TransactionStatus::COMPLETED->value
        ]);

        // Assert
        $this->expectException(DailyLimitExceededException::class);
        $this->expectExceptionMessage('Daily juice limit exceeded');

        // Act
        $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 5);
    }

    public function test_throws_exception_when_daily_point_limit_exceeded()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'daily_juice_limit' => 5,
            'daily_point_limit' => 15
        ]);
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'points_balance' => 50,
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::ACTIVE
        ]);
        $category = ProductCategory::factory()->create(['name' => 'beverages']);
        $product = Product::factory()->create([
            'price_points' => 10,
            'product_category_id' => $category->product_category_id
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 5
        ]);

        // Create existing transaction that uses 10 points
        Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'points_deducted' => 10,
            'transaction_time' => Carbon::today(),
            'status' => TransactionStatus::COMPLETED->value
        ]);

        // Assert
        $this->expectException(DailyLimitExceededException::class);
        $this->expectExceptionMessage('Daily point limit would be exceeded');

        // Act
        $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 5);
    }

    public function test_checks_daily_snack_limit()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'daily_snack_limit' => 1,
            'daily_point_limit' => 100
        ]);
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'points_balance' => 50,
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::ACTIVE
        ]);
        $category = ProductCategory::factory()->create(['name' => 'snacks']);
        $product = Product::factory()->create([
            'price_points' => 10,
            'product_category_id' => $category->product_category_id
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 5
        ]);

        // Create existing snack transaction for today
        Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'product_id' => $product->product_id,
            'transaction_time' => Carbon::today(),
            'status' => TransactionStatus::COMPLETED->value
        ]);

        // Assert
        $this->expectException(DailyLimitExceededException::class);
        $this->expectExceptionMessage('Daily snack limit exceeded');

        // Act
        $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 5);
    }

    public function test_checks_daily_meal_limit()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'daily_meal_limit' => 1,
            'daily_point_limit' => 100
        ]);
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'points_balance' => 50,
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::ACTIVE
        ]);
        $category = ProductCategory::factory()->create(['name' => 'meals']);
        $product = Product::factory()->create([
            'price_points' => 10,
            'product_category_id' => $category->product_category_id
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 5
        ]);

        // Create existing meal transaction for today
        Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'product_id' => $product->product_id,
            'transaction_time' => Carbon::today(),
            'status' => TransactionStatus::COMPLETED->value
        ]);

        // Assert
        $this->expectException(DailyLimitExceededException::class);
        $this->expectExceptionMessage('Daily meal limit exceeded');

        // Act
        $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 5);
    }

    public function test_successful_purchase_with_exact_points()
    {
        // Arrange
        $classification = Classification::factory()->create([
            'daily_juice_limit' => 5,
            'daily_point_limit' => 100
        ]);
        $card = Card::factory()->create([
            'card_number' => 'TEST123',
            'points_balance' => 10,
            'status' => CardStatus::ACTIVE
        ]);
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id,
            'status' => EmployeeStatus::ACTIVE
        ]);
        $machine = Machine::factory()->create([
            'status' => MachineStatus::ACTIVE
        ]);
        $category = ProductCategory::factory()->create(['name' => 'beverages']);
        $product = Product::factory()->create([
            'name' => 'Water',
            'price_points' => 10,
            'product_category_id' => $category->product_category_id
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 1
        ]);

        // Act
        $result = $this->purchaseProductAction->execute('TEST123', $machine->machine_id, 1);

        // Assert
        $this->assertEquals(0, $result['remaining_balance']);
        $this->assertEquals('Water', $result['product']['name']);
    }
}
