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
        // Update any null values to current timestamp
        \DB::table('test_attempts')
            ->whereNull('started_at')
            ->update(['started_at' => \DB::raw('CURRENT_TIMESTAMP')]);
        
        // Change the column to not nullable
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->change();
        });
    }
};
