<?php

namespace App\Console\Commands;

use App\Models\Machine;
use App\Services\MachineService;
use App\Services\ProductService;
use Illuminate\Console\Command;

class FillSlotsCommand extends Command
{

    public function __construct(
        private readonly ProductService $productService,
        private readonly MachineService $machineService,
    )
    {
        parent::__construct();
    }

    protected $signature = 'slots:fill {--machine= : Specific machine ID to fill} {--empty-only : Only fill empty slots}';

    protected $description = 'Fill all machine slots with random products and quantities';

    public function handle(): int
    {
        $this->info('Starting to fill slots with products...');

        // Get available products
        $products = $this->productService->getAllProducts();

        if ($products->isEmpty()) {
            $this->error('No products found in the database!');
            return 1;
        }

        // Get machines to process
        $machineId = $this->option('machine');
        $emptyOnly = $this->option('empty-only');

        if ($machineId) {
            $machines = Machine::where('machine_id', $machineId)->get();
            if ($machines->isEmpty()) {
                $this->error("Machine with ID {$machineId} not found!");
                return 1;
            }
        } else {
            $machines = $this->machineService->getAllMachines();
        }

        $totalSlotsFilled = 0;

        foreach ($machines as $machine) {
            $this->info("Processing Machine: {$machine->name} (ID: {$machine->machine_id})");

            // Get slots for this machine
            $slotsQuery = $machine->slots();

            if ($emptyOnly) {
                $slotsQuery->whereNull('product_id');
            }

            $slots = $slotsQuery->get();

            $machineFilled = 0;

            foreach ($slots as $slot) {
                // Randomly select a product
                $randomProduct = $products->random();

                // Random quantity between 1 and 10
                $randomQuantity = rand(1, 10);

                // Update the slot
                $slot->update([
                    'product_id' => $randomProduct->product_id,
                    'quantity' => $randomQuantity
                ]);

                $machineFilled++;
                $totalSlotsFilled++;

                $this->line("  Slot {$slot->number}: {$randomProduct->name} (Qty: {$randomQuantity})");
            }

            $this->info("  → Filled {$machineFilled} slots in {$machine->name}");
        }

        $this->newLine();
        $this->info("✅ Successfully filled {$totalSlotsFilled} slots across " . $machines->count() . " machine(s)!");

        return 0;
    }
}
