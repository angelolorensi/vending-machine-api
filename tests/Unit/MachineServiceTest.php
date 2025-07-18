<?php

namespace Tests\Unit;

use App\Services\MachineService;
use App\Models\Machine;
use App\Models\Slot;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Enums\MachineStatus;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MachineServiceTest extends TestCase
{
    use RefreshDatabase;

    private MachineService $machineService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->machineService = new MachineService();
    }

    public function test_can_get_machine_by_id()
    {
        // Arrange
        $machine = Machine::factory()->create([
            'name' => 'Test Machine',
            'location' => 'Test Location',
            'status' => MachineStatus::ACTIVE
        ]);

        // Act
        $result = $this->machineService->getMachineById($machine->machine_id);

        // Assert
        $this->assertEquals($machine->machine_id, $result->machine_id);
        $this->assertEquals('Test Machine', $result->name);
        $this->assertEquals('Test Location', $result->location);
        $this->assertEquals(MachineStatus::ACTIVE, $result->status);
        $this->assertTrue($result->relationLoaded('slots'));
    }

    public function test_throws_exception_when_machine_not_found_by_id()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Machine not found');

        $this->machineService->getMachineById(999);
    }

    public function test_can_create_machine()
    {
        // Arrange
        $machineData = [
            'name' => 'New Machine',
            'location' => 'Main Lobby',
            'status' => MachineStatus::ACTIVE
        ];

        // Act
        $result = $this->machineService->createMachine($machineData);

        // Assert
        $this->assertInstanceOf(Machine::class, $result);
        $this->assertEquals('New Machine', $result->name);
        $this->assertEquals('Main Lobby', $result->location);
        $this->assertEquals(MachineStatus::ACTIVE, $result->status);

        $this->assertDatabaseHas('machines', [
            'name' => 'New Machine',
            'location' => 'Main Lobby',
            'status' => MachineStatus::ACTIVE
        ]);
    }

    public function test_create_machine_automatically_creates_30_slots()
    {
        // Arrange
        $machineData = [
            'name' => 'Slot Test Machine',
            'location' => 'Test Area',
            'status' => MachineStatus::ACTIVE
        ];

        // Act
        $result = $this->machineService->createMachine($machineData);

        // Assert
        $this->assertDatabaseCount('slots', 30);

        // Verify all slot numbers from 1 to 30 exist
        for ($i = 1; $i <= 30; $i++) {
            $this->assertDatabaseHas('slots', [
                'machine_id' => $result->machine_id,
                'number' => $i,
                'product_id' => null
            ]);
        }
    }

    public function test_created_slots_are_initially_empty()
    {
        // Arrange
        $machineData = [
            'name' => 'Empty Slots Machine',
            'location' => 'Test Location',
            'status' => MachineStatus::ACTIVE
        ];

        // Act
        $result = $this->machineService->createMachine($machineData);

        // Assert
        $slots = Slot::where('machine_id', $result->machine_id)->get();
        $this->assertCount(30, $slots);

        foreach ($slots as $slot) {
            $this->assertNull($slot->product_id);
        }
    }

    public function test_can_update_machine()
    {
        // Arrange
        $machine = Machine::factory()->create([
            'name' => 'Original Name',
            'location' => 'Original Location',
            'status' => MachineStatus::ACTIVE
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'location' => 'Updated Location',
            'status' => MachineStatus::INACTIVE
        ];

        // Act
        $result = $this->machineService->updateMachine($machine->machine_id, $updateData);

        // Assert
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals('Updated Location', $result->location);
        $this->assertEquals(MachineStatus::INACTIVE, $result->status);

        $this->assertDatabaseHas('machines', [
            'machine_id' => $machine->machine_id,
            'name' => 'Updated Name',
            'location' => 'Updated Location',
            'status' => MachineStatus::INACTIVE
        ]);
    }

    public function test_throws_exception_when_updating_nonexistent_machine()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Machine not found');

        $this->machineService->updateMachine(999, ['name' => 'Updated']);
    }

    public function test_can_delete_machine()
    {
        // Arrange
        $machine = Machine::factory()->create();

        // Act
        $result = $this->machineService->deleteMachine($machine->machine_id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('machines', [
            'machine_id' => $machine->machine_id
        ]);
    }

    public function test_throws_exception_when_deleting_nonexistent_machine()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Machine not found');

        $this->machineService->deleteMachine(999);
    }

    public function test_get_machine_by_id_loads_slots_with_products()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['product_category_id' => $category->product_category_id]);

        Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'number' => 1,
            'product_id' => $product->product_id
        ]);

        Slot::factory()->create([
            'machine_id' => $machine->machine_id,
            'number' => 2,
            'product_id' => null
        ]);

        // Act
        $result = $this->machineService->getMachineById($machine->machine_id);

        // Assert
        $this->assertCount(2, $result->slots);

        $slotWithProduct = $result->slots->where('number', 1)->first();
        $this->assertNotNull($slotWithProduct->product);
        $this->assertEquals($product->product_id, $slotWithProduct->product->product_id);

        $emptySlot = $result->slots->where('number', 2)->first();
        $this->assertNull($emptySlot->product);
    }

    public function test_update_returns_fresh_instance()
    {
        // Arrange
        $machine = Machine::factory()->create([
            'name' => 'Original Name',
            'status' => MachineStatus::ACTIVE
        ]);

        // Act
        $result = $this->machineService->updateMachine($machine->machine_id, [
            'name' => 'Updated Name',
            'status' => MachineStatus::INACTIVE
        ]);

        // Assert
        $this->assertEquals('Updated Name', $result->name);
        $this->assertEquals(MachineStatus::INACTIVE, $result->status);

        // Verify original instance wasn't modified
        $this->assertEquals('Original Name', $machine->name);
        $this->assertEquals(MachineStatus::ACTIVE, $machine->status);

        // Verify database was updated
        $this->assertDatabaseHas('machines', [
            'machine_id' => $machine->machine_id,
            'name' => 'Updated Name',
            'status' => MachineStatus::INACTIVE
        ]);
    }

    public function test_create_machine_with_minimal_data()
    {
        // Arrange
        $machineData = [
            'name' => 'Minimal Machine',
            'location' => 'Test',
            'status' => MachineStatus::ACTIVE
        ];

        // Act
        $result = $this->machineService->createMachine($machineData);

        // Assert
        $this->assertNotNull($result->machine_id);
        $this->assertEquals('Minimal Machine', $result->name);
        $this->assertDatabaseCount('slots', 30);
    }

    public function test_delete_machine_removes_associated_slots()
    {
        // Arrange
        $machine = Machine::factory()->create();
        $slot = Slot::factory()->create(['machine_id' => $machine->machine_id]);

        // Act
        $this->machineService->deleteMachine($machine->machine_id);

        // Assert
        $this->assertDatabaseMissing('machines', [
            'machine_id' => $machine->machine_id
        ]);

        // Assuming cascade delete is set up, slots should be removed too
        $this->assertDatabaseMissing('slots', [
            'slot_id' => $slot->slot_id
        ]);
    }

    public function test_created_slots_have_sequential_numbers()
    {
        // Arrange
        $machineData = [
            'name' => 'Sequential Test',
            'location' => 'Test Location',
            'status' => MachineStatus::ACTIVE
        ];

        // Act
        $result = $this->machineService->createMachine($machineData);

        // Assert
        $slots = Slot::where('machine_id', $result->machine_id)
            ->orderBy('number')
            ->get();

        $this->assertCount(30, $slots);

        for ($i = 0; $i < 30; $i++) {
            $this->assertEquals($i + 1, $slots[$i]->number);
        }
    }
}
