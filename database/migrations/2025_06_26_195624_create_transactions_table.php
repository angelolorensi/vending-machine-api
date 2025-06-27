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
            $table->foreignId('employee_id');
            $table->foreignId('machine_id');
            $table->foreignId('slot_id');
            $table->foreignId('product_id');
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
