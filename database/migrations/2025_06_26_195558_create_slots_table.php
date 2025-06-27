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
        Schema::create('slots', function (Blueprint $table) {
            $table->id('slot_id');
            $table->integer('number');
            $table->foreignId('machine_id')
                ->constrained('machines', 'machine_id')
                ->onDelete('cascade');
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products', 'product_id')
                ->onDelete('set null');
            $table->timestamps();

            $table->unique(['machine_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
