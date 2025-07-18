<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE transactions DROP CONSTRAINT IF EXISTS transactions_status_check");

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('status', 50)->change();
        });

        DB::statement("UPDATE transactions SET status = 'completed' WHERE status = 'success'");
        DB::statement("UPDATE transactions SET status = 'failed' WHERE status = 'failure'");
    }

    public function down(): void
    {
        DB::statement("UPDATE transactions SET status = 'success' WHERE status = 'completed'");
        DB::statement("UPDATE transactions SET status = 'failure' WHERE status = 'failed'");

        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('status', ['success', 'failure'])->change();
        });
    }
};
