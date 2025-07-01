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
        Schema::create('classifications', function (Blueprint $table) {
            $table->id('classification_id');
            $table->string('name');
            $table->integer('daily_juice_limit');
            $table->integer('daily_meal_limit');
            $table->integer('daily_snack_limit');
            $table->integer('daily_point_limit');
            $table->integer('daily_point_recharge_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classifications');
    }
};
