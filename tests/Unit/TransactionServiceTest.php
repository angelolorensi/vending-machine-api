<?php

namespace Tests\Unit;

use App\Enums\TransactionStatus;
use App\Services\TransactionService;
use App\Models\Transaction;
use App\Models\Employee;
use App\Models\Card;
use App\Models\Machine;
use App\Models\Slot;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Classification;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionService = new TransactionService();
    }

    public function test_can_get_transaction_by_id()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id
        ]);
        $machine = Machine::factory()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['product_category_id' => $category->product_category_id]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id
        ]);

        $transaction = Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'card_id' => $card->card_id,
            'machine_id' => $machine->machine_id,
            'slot_id' => $slot->slot_id,
            'product_id' => $product->product_id,
            'points_deducted' => 150,
            'status' => TransactionStatus::COMPLETED
        ]);

        // Act
        $result = $this->transactionService->getTransactionById($transaction->transaction_id);

        // Assert
        $this->assertEquals($transaction->transaction_id, $result->transaction_id);
        $this->assertEquals($employee->employee_id, $result->employee_id);
        $this->assertEquals($card->card_id, $result->card_id);
        $this->assertEquals($machine->machine_id, $result->machine_id);
        $this->assertEquals($slot->slot_id, $result->slot_id);
        $this->assertEquals($product->product_id, $result->product_id);
        $this->assertEquals(150, $result->points_deducted);
        $this->assertEquals(TransactionStatus::COMPLETED, $result->status);
        $this->assertTrue($result->relationLoaded('employee'));
        $this->assertTrue($result->relationLoaded('card'));
        $this->assertTrue($result->relationLoaded('machine'));
        $this->assertTrue($result->relationLoaded('slot'));
        $this->assertTrue($result->relationLoaded('product'));
    }

    public function test_throws_exception_when_transaction_not_found_by_id()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Transaction not found');

        $this->transactionService->getTransactionById(999);
    }

    public function test_can_create_transaction()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $card = Card::factory()->create();
        $employee = Employee::factory()->create([
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id
        ]);
        $machine = Machine::factory()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['product_category_id' => $category->product_category_id]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id
        ]);

        $transactionData = [
            'employee_id' => $employee->employee_id,
            'card_id' => $card->card_id,
            'machine_id' => $machine->machine_id,
            'slot_id' => $slot->slot_id,
            'product_id' => $product->product_id,
            'points_deducted' => 200,
            'status' => TransactionStatus::COMPLETED,
            'transaction_time' => now()
        ];

        // Act
        $result = $this->transactionService->createTransaction($transactionData);

        // Assert
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals($employee->employee_id, $result->employee_id);
        $this->assertEquals($card->card_id, $result->card_id);
        $this->assertEquals($machine->machine_id, $result->machine_id);
        $this->assertEquals($slot->slot_id, $result->slot_id);
        $this->assertEquals($product->product_id, $result->product_id);
        $this->assertEquals(200, $result->points_deducted);
        $this->assertEquals(TransactionStatus::COMPLETED, $result->status);

        $this->assertDatabaseHas('transactions', [
            'employee_id' => $employee->employee_id,
            'card_id' => $card->card_id,
            'machine_id' => $machine->machine_id,
            'slot_id' => $slot->slot_id,
            'product_id' => $product->product_id,
            'points_deducted' => 200,
            'status' => TransactionStatus::COMPLETED
        ]);
    }

    public function test_can_update_transaction()
    {
        // Arrange
        $transaction = Transaction::factory()->create([
            'points_deducted' => 100,
            'status' => TransactionStatus::PENDING
        ]);

        $updateData = [
            'points_deducted' => 150,
            'status' => TransactionStatus::COMPLETED
        ];

        // Act
        $result = $this->transactionService->updateTransaction($transaction->transaction_id, $updateData);

        // Assert
        $this->assertEquals(150, $result->points_deducted);
        $this->assertEquals(TransactionStatus::COMPLETED, $result->status);

        $this->assertDatabaseHas('transactions', [
            'transaction_id' => $transaction->transaction_id,
            'points_deducted' => 150,
            'status' => TransactionStatus::COMPLETED
        ]);
    }

    public function test_throws_exception_when_updating_nonexistent_transaction()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Transaction not found');

        $this->transactionService->updateTransaction(999, ['status' => TransactionStatus::COMPLETED]);
    }

    public function test_can_delete_transaction()
    {
        // Arrange
        $transaction = Transaction::factory()->create();

        // Act
        $result = $this->transactionService->deleteTransaction($transaction->transaction_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('transactions', [
            'transaction_id' => $transaction->transaction_id
        ]);
    }

    public function test_throws_exception_when_deleting_nonexistent_transaction()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Transaction not found');

        $this->transactionService->deleteTransaction(999);
    }

    public function test_can_get_employee_daily_transactions()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create(['classification_id' => $classification->classification_id]);
        $category = ProductCategory::factory()->create();
        $product1 = Product::factory()->create(['product_category_id' => $category->product_category_id]);
        $product2 = Product::factory()->create(['product_category_id' => $category->product_category_id]);

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayTransaction1 = Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'product_id' => $product1->product_id,
            'transaction_time' => $today,
            'status' => TransactionStatus::COMPLETED
        ]);
        $todayTransaction2 = Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'product_id' => $product2->product_id,
            'transaction_time' => $today,
            'status' => TransactionStatus::COMPLETED
        ]);

        Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'product_id' => $product1->product_id,
            'transaction_time' => $yesterday,
            'status' => TransactionStatus::COMPLETED
        ]);

        Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'product_id' => $product1->product_id,
            'transaction_time' => $today,
            'status' => TransactionStatus::FAILED
        ]);

        // Act
        $result = $this->transactionService->getEmployeeDailyTransactions(
            $employee->employee_id,
            $today->toDateString()
        );

        // Assert
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('transaction_id', $todayTransaction1->transaction_id));
        $this->assertTrue($result->contains('transaction_id', $todayTransaction2->transaction_id));
    }

    public function test_get_employee_daily_transactions_loads_product_and_category()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create(['classification_id' => $classification->classification_id]);
        $category = ProductCategory::factory()->create(['name' => 'Beverages']);
        $product = Product::factory()->create([
            'name' => 'Coca Cola',
            'product_category_id' => $category->product_category_id
        ]);

        $today = Carbon::today();
        Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'product_id' => $product->product_id,
            'transaction_time' => $today,
            'status' => TransactionStatus::COMPLETED
        ]);

        // Act
        $result = $this->transactionService->getEmployeeDailyTransactions(
            $employee->employee_id,
            $today->toDateString()
        );

        // Assert
        $transaction = $result->first();
        $this->assertNotNull($transaction->product);
        $this->assertEquals('Coca Cola', $transaction->product->name);
        $this->assertNotNull($transaction->product->productCategory);
        $this->assertEquals('Beverages', $transaction->product->productCategory->name);
    }

    public function test_update_returns_fresh_instance()
    {
        // Arrange
        $transaction = Transaction::factory()->create([
            'points_deducted' => 100,
            'status' => TransactionStatus::PENDING
        ]);

        // Act
        $result = $this->transactionService->updateTransaction(
            $transaction->transaction_id,
            ['points_deducted' => 250, 'status' => TransactionStatus::COMPLETED]
        );

        // Assert
        $this->assertEquals(250, $result->points_deducted);
        $this->assertEquals(TransactionStatus::COMPLETED, $result->status);

        $this->assertEquals(100, $transaction->points_deducted);
        $this->assertEquals(TransactionStatus::PENDING, $transaction->status);

        $this->assertDatabaseHas('transactions', [
            'transaction_id' => $transaction->transaction_id,
            'points_deducted' => 250,
            'status' => TransactionStatus::COMPLETED
        ]);
    }

    public function test_get_transaction_loads_all_relationships()
    {
        // Arrange
        $classification = Classification::factory()->create(['name' => 'Manager']);
        $card = Card::factory()->create(['card_number' => 'TEST123']);
        $employee = Employee::factory()->create([
            'name' => 'John Doe',
            'classification_id' => $classification->classification_id,
            'card_id' => $card->card_id
        ]);
        $machine = Machine::factory()->create(['name' => 'Vending Machine 1']);
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'product_category_id' => $category->product_category_id
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 5
        ]);

        $transaction = Transaction::factory()->create([
            'employee_id' => $employee->employee_id,
            'card_id' => $card->card_id,
            'machine_id' => $machine->machine_id,
            'slot_id' => $slot->slot_id,
            'product_id' => $product->product_id
        ]);

        // Act
        $result = $this->transactionService->getTransactionById($transaction->transaction_id);

        // Assert
        $this->assertEquals('John Doe', $result->employee->name);
        $this->assertEquals('Manager', $result->employee->classification->name);
        $this->assertEquals('TEST123', $result->card->card_number);
        $this->assertEquals('Vending Machine 1', $result->machine->name);
        $this->assertEquals(5, $result->slot->number);
        $this->assertEquals('Test Product', $result->product->name);
    }

    public function test_get_employee_daily_transactions_returns_empty_collection_when_no_transactions()
    {
        // Arrange
        $classification = Classification::factory()->create();
        $employee = Employee::factory()->create(['classification_id' => $classification->classification_id]);
        $today = Carbon::today();

        // Act
        $result = $this->transactionService->getEmployeeDailyTransactions(
            $employee->employee_id,
            $today->toDateString()
        );

        // Assert
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }
}
