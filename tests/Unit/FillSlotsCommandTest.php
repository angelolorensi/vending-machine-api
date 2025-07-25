<?php

namespace Tests\Unit;

use App\Models\Machine;
use App\Models\Product;
use App\Models\Slot;
use App\Services\MachineService;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FillSlotsCommandTest extends TestCase
{
    use RefreshDatabase;

    private MachineService $machineService;
    private ProductService $productService;
    private ProductCategoryService $productCategoryService;

    protected function setUp(): void
    {
        parent::setUp();

        // Initialize services
        $this->machineService = app(MachineService::class);
        $this->productService = app(ProductService::class);
        $this->productCategoryService = app(ProductCategoryService::class);

        // Create test data
        $this->createTestData();
    }

    private function createTestData(): void
    {
        // Create product categories using service
        $snacksCategory = $this->productCategoryService->createProductCategory([
            'name' => 'Snacks',
            'color' => '#fff500'
        ]);

        $beveragesCategory = $this->productCategoryService->createProductCategory([
            'name' => 'Beverages',
            'color' => '#ff0000'
        ]);

        // Create products using service
        $this->productService->createProduct([
            'name' => 'Potato Chips',
            'description' => 'Classic salted potato chips',
            'price_points' => 5,
            'product_category_id' => $snacksCategory->product_category_id
        ]);

        $this->productService->createProduct([
            'name' => 'Coca Cola',
            'description' => 'Classic cola drink',
            'price_points' => 6,
            'product_category_id' => $beveragesCategory->product_category_id
        ]);

        $this->productService->createProduct([
            'name' => 'Water',
            'description' => 'Bottled water',
            'price_points' => 2,
            'product_category_id' => $beveragesCategory->product_category_id
        ]);

        // Create machines using service (this creates 30 slots per machine by default)
        $this->machineService->createMachine([
            'name' => 'Test Machine 1',
            'location' => 'Test Location 1',
            'status' => 'active'
        ]);

        $this->machineService->createMachine([
            'name' => 'Test Machine 2',
            'location' => 'Test Location 2',
            'status' => 'active'
        ]);
    }

    public function test_fills_all_slots_in_all_machines(): void
    {
        // Verify initial state - all slots should be empty
        $this->assertEquals(0, Slot::whereNotNull('product_id')->count());

        // Run command
        $this->artisan('slots:fill')
            ->expectsOutput('Starting to fill slots with products...')
            ->expectsOutput('✅ Successfully filled 60 slots across 2 machine(s)!')
            ->assertExitCode(0);

        // Verify all slots are now filled (30 slots per machine * 2 machines = 60)
        $this->assertEquals(60, Slot::whereNotNull('product_id')->count());

        // Verify quantities are set (1-10 range)
        $slots = Slot::whereNotNull('product_id')->get();
        foreach ($slots as $slot) {
            $this->assertGreaterThanOrEqual(1, $slot->quantity);
            $this->assertLessThanOrEqual(10, $slot->quantity);
            $this->assertNotNull($slot->product_id);
        }
    }

    public function test_fills_only_empty_slots_with_empty_only_option(): void
    {
        // Pre-fill one slot
        $slot = Slot::first();
        $product = Product::first();
        $slot->update([
            'product_id' => $product->product_id,
            'quantity' => 5
        ]);

        // Run command with --empty-only
        $this->artisan('slots:fill --empty-only')
            ->expectsOutput('Starting to fill slots with products...')
            ->expectsOutput('✅ Successfully filled 59 slots across 2 machine(s)!')
            ->assertExitCode(0);

        // Verify the pre-filled slot wasn't changed
        $slot->refresh();
        $this->assertEquals($product->product_id, $slot->product_id);
        $this->assertEquals(5, $slot->quantity);

        // Verify other slots are filled (60 total - 1 pre-filled = 59)
        $this->assertEquals(60, Slot::whereNotNull('product_id')->count());
    }

    public function test_fills_specific_machine_only(): void
    {
        $machine = Machine::first();

        $this->artisan("slots:fill --machine={$machine->machine_id}")
            ->expectsOutput('Starting to fill slots with products...')
            ->expectsOutput("Processing Machine: {$machine->name} (ID: {$machine->machine_id})")
            ->expectsOutput('✅ Successfully filled 30 slots across 1 machine(s)!')
            ->assertExitCode(0);

        // Verify only the specific machine's slots are filled
        $this->assertEquals(30, $machine->slots()->whereNotNull('product_id')->count());

        // Verify other machine's slots are still empty
        $otherMachine = Machine::where('machine_id', '!=', $machine->machine_id)->first();
        $this->assertEquals(0, $otherMachine->slots()->whereNotNull('product_id')->count());
    }

    public function test_combines_machine_and_empty_only_options(): void
    {
        $machine = Machine::first();

        // Pre-fill one slot in the target machine
        $slot = $machine->slots()->first();
        $product = Product::first();
        $slot->update([
            'product_id' => $product->product_id,
            'quantity' => 3
        ]);

        $this->artisan("slots:fill --machine={$machine->machine_id} --empty-only")
            ->expectsOutput('Starting to fill slots with products...')
            ->expectsOutput('✅ Successfully filled 29 slots across 1 machine(s)!')
            ->assertExitCode(0);

        // Verify pre-filled slot wasn't changed
        $slot->refresh();
        $this->assertEquals($product->product_id, $slot->product_id);
        $this->assertEquals(3, $slot->quantity);

        // Verify other slots in the machine are filled (30 total - 1 pre-filled = 29 + 1 = 30)
        $this->assertEquals(30, $machine->slots()->whereNotNull('product_id')->count());
    }

    public function test_handles_no_products_error(): void
    {
        // Delete all products
        Product::truncate();

        $this->artisan('slots:fill')
            ->expectsOutput('Starting to fill slots with products...')
            ->expectsOutput('No products found in the database!')
            ->assertExitCode(1);

        // Verify no slots were filled
        $this->assertEquals(0, Slot::whereNotNull('product_id')->count());
    }

    public function test_handles_invalid_machine_id(): void
    {
        $this->artisan('slots:fill --machine=999')
            ->expectsOutput('Starting to fill slots with products...')
            ->expectsOutput('Machine with ID 999 not found!')
            ->assertExitCode(1);

        // Verify no slots were filled
        $this->assertEquals(0, Slot::whereNotNull('product_id')->count());
    }

    public function test_assigns_valid_products_to_slots(): void
    {
        $this->artisan('slots:fill')
            ->assertExitCode(0);

        $productIds = Product::pluck('product_id')->toArray();

        // Verify all assigned products exist in the database
        $slots = Slot::whereNotNull('product_id')->get();
        foreach ($slots as $slot) {
            $this->assertContains($slot->product_id, $productIds);
        }
    }

    public function test_produces_expected_output_format(): void
    {
        $machines = $this->machineService->getAllMachines();
        $machine = $machines->first();

        $this->artisan("slots:fill --machine={$machine->machine_id}")
            ->expectsOutputToContain('Starting to fill slots with products...')
            ->expectsOutputToContain("Processing Machine: {$machine->name}")
            ->expectsOutputToContain('Slot 1:')
            ->expectsOutputToContain('(Qty:')
            ->expectsOutputToContain('→ Filled 30 slots in')
            ->expectsOutputToContain('✅ Successfully filled')
            ->assertExitCode(0);
    }

    public function test_handles_machine_with_no_empty_slots(): void
    {
        $machines = $this->machineService->getAllMachines();
        $machine = $machines->first();
        $product = Product::first();

        // Fill all slots in the machine
        $machine->slots->each(function ($slot) use ($product) {
            $slot->update([
                'product_id' => $product->product_id,
                'quantity' => 5
            ]);
        });

        $this->artisan("slots:fill --machine={$machine->machine_id} --empty-only")
            ->expectsOutput('Starting to fill slots with products...')
            ->expectsOutput('✅ Successfully filled 0 slots across 1 machine(s)!')
            ->assertExitCode(0);
    }

    public function test_random_quantity_distribution(): void
    {
        $this->artisan('slots:fill')
            ->assertExitCode(0);

        $quantities = Slot::whereNotNull('product_id')->pluck('quantity')->toArray();

        // Verify we have a distribution of quantities (not all the same)
        $uniqueQuantities = array_unique($quantities);
        $this->assertGreaterThan(1, count($uniqueQuantities), 'Expected variety in random quantities');

        // Verify all quantities are in valid range
        foreach ($quantities as $quantity) {
            $this->assertGreaterThanOrEqual(1, $quantity);
            $this->assertLessThanOrEqual(10, $quantity);
        }
    }
}
