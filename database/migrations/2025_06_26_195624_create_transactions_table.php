<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->foreignId('employee_id')
                ->constrained('employees', 'employee_id')
                ->onDelete('restrict');
            $table->foreignId('card_id')
                ->after('employee_id')
                ->constrained('cards', 'card_id')
                ->onDelete('restrict');
            $table->foreignId('machine_id')
                ->constrained('machines', 'machine_id')
                ->onDelete('restrict');
            $table->foreignId('slot_id')
                ->constrained('slots', 'slot_id')
                ->onDelete('restrict');
            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('restrict');
            $table->integer('points_deducted');
            $table->timestamp('transaction_time');
            $table->enum('status', ['success', 'failure']);
            $table->string('failure_reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
