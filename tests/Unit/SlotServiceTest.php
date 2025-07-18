<?php

namespace Tests\Unit;

use App\Services\SlotService;
use App\Models\Slot;
use App\Models\Machine;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlotServiceTest extends TestCase
{
    use RefreshDatabase;

    private SlotService $slotService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->slotService = new SlotService();
    }

    public function test_can_get_slot_by_id()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['product_category_id' => $category->product_category_id]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 5
        ]);

        // Act
        $result = $this->slotService->getSlotById($slot->slot_id);

        // Assert
        $this->assertEquals($slot->slot_id, $result->slot_id);
        $this->assertEquals($machine->machine_id, $result->machine_id);
        $this->assertEquals($product->product_id, $result->product_id);
        $this->assertEquals(5, $result->number);
        $this->assertTrue($result->relationLoaded('machine'));
        $this->assertTrue($result->relationLoaded('product'));
    }

    public function test_throws_exception_when_slot_not_found_by_id()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Slot not found');

        $this->slotService->getSlotById(999);
    }

    public function test_can_get_slot_by_machine_and_number()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['product_category_id' => $category->product_category_id]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 10
        ]);

        // Act
        $result = $this->slotService->getSlotByMachineAndNumber($machine->machine_id, 10);

        // Assert
        $this->assertEquals($slot->slot_id, $result->slot_id);
        $this->assertEquals($machine->machine_id, $result->machine_id);
        $this->assertEquals(10, $result->number);
        $this->assertTrue($result->relationLoaded('product'));
    }

    public function test_throws_exception_when_slot_not_found_by_machine_and_number()
    {
        // Arrange
        $machine = Machine::factory()->create();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Slot not found');

        $this->slotService->getSlotByMachineAndNumber($machine->machine_id, 999);
    }

    public function test_can_create_slot()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['product_category_id' => $category->product_category_id]);
        $slotData = [
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 15
        ];

        // Act
        $result = $this->slotService->createSlot($slotData);

        // Assert
        $this->assertInstanceOf(Slot::class, $result);
        $this->assertEquals($machine->machine_id, $result->machine_id);
        $this->assertEquals($product->product_id, $result->product_id);
        $this->assertEquals(15, $result->number);

        $this->assertDatabaseHas('slots', [
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id,
            'number' => 15
        ]);
    }

    public function test_can_create_empty_slot()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $slotData = [
            'machine_id' => $machine->machine_id,
            'product_id' => null,
            'number' => 20
        ];

        // Act
        $result = $this->slotService->createSlot($slotData);

        // Assert
        $this->assertEquals($machine->machine_id, $result->machine_id);
        $this->assertNull($result->product_id);
        $this->assertEquals(20, $result->number);
    }

    public function test_can_update_slot()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $oldProduct = Product::factory()->create();
        $newProduct = Product::factory()->create();
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $oldProduct->product_id,
            'number' => 5
        ]);

        $updateData = [
            'product_id' => $newProduct->product_id,
            'number' => 10
        ];

        // Act
        $result = $this->slotService->updateSlot($slot->slot_id, $updateData);

        // Assert
        $this->assertEquals($newProduct->product_id, $result->product_id);
        $this->assertEquals(10, $result->number);

        $this->assertDatabaseHas('slots', [
            'slot_id' => $slot->slot_id,
            'product_id' => $newProduct->product_id,
            'number' => 10
        ]);
    }

    public function test_throws_exception_when_updating_nonexistent_slot()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Slot not found');

        $this->slotService->updateSlot(999, ['number' => 1]);
    }

    public function test_can_delete_slot()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id
        ]);

        // Act
        $result = $this->slotService->deleteSlot($slot->slot_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('slots', [
            'slot_id' => $slot->slot_id
        ]);
    }

    public function test_throws_exception_when_deleting_nonexistent_slot()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Slot not found');

        $this->slotService->deleteSlot(999);
    }

    public function test_get_slot_by_id_loads_machine_relationship()
    {
        // Arrange
        $machine = Machine::factory()->create(['name' => 'Test Machine']);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'number' => 1
        ]);

        // Act
        $result = $this->slotService->getSlotById($slot->slot_id);

        // Assert
        $this->assertNotNull($result->machine);
        $this->assertEquals('Test Machine', $result->machine->name);
        $this->assertEquals($machine->machine_id, $result->machine->machine_id);
    }

    public function test_get_slot_by_id_loads_product_relationship()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'product_category_id' => $category->product_category_id
        ]);
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id
        ]);

        // Act
        $result = $this->slotService->getSlotById($slot->slot_id);

        // Assert
        $this->assertNotNull($result->product);
        $this->assertEquals('Test Product', $result->product->name);
        $this->assertEquals($product->product_id, $result->product->product_id);
    }

    public function test_update_returns_fresh_instance()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $oldProduct = Product::factory()->create();
        $newProduct = Product::factory()->create();
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $oldProduct->product_id,
            'number' => 5
        ]);

        // Act
        $result = $this->slotService->updateSlot($slot->slot_id, [
            'product_id' => $newProduct->product_id,
            'number' => 15
        ]);

        // Assert
        $this->assertEquals($newProduct->product_id, $result->product_id);
        $this->assertEquals(15, $result->number);

        // Verify original instance wasn't modified
        $this->assertEquals($oldProduct->product_id, $slot->product_id);
        $this->assertEquals(5, $slot->number);

        // Verify database was updated
        $this->assertDatabaseHas('slots', [
            'slot_id' => $slot->slot_id,
            'product_id' => $newProduct->product_id,
            'number' => 15
        ]);
    }

    public function test_can_empty_slot_by_removing_product()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $product = Product::factory()->create();
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => $product->product_id
        ]);

        // Act
        $result = $this->slotService->updateSlot($slot->slot_id, [
            'product_id' => null
        ]);

        // Assert
        $this->assertNull($result->product_id);
        $this->assertDatabaseHas('slots', [
            'slot_id' => $slot->slot_id,
            'product_id' => null
        ]);
    }

    public function test_can_assign_product_to_empty_slot()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $product = Product::factory()->create();
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => null
        ]);

        // Act
        $result = $this->slotService->updateSlot($slot->slot_id, [
            'product_id' => $product->product_id
        ]);

        // Assert
        $this->assertEquals($product->product_id, $result->product_id);
        $this->assertDatabaseHas('slots', [
            'slot_id' => $slot->slot_id,
            'product_id' => $product->product_id
        ]);
    }

    public function test_get_slot_by_machine_and_number_with_empty_slot()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $slot = Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'product_id' => null,
            'number' => 25
        ]);

        // Act
        $result = $this->slotService->getSlotByMachineAndNumber($machine->machine_id, 25);

        // Assert
        $this->assertEquals($slot->slot_id, $result->slot_id);
        $this->assertNull($result->product_id);
        $this->assertNull($result->product);
    }
}