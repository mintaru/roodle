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
        Schema::table('temporary_answers', function (Blueprint $table) {
            // Добавляем ссылку на конкретную попытку теста
            $table->foreignId('test_attempt_id')->nullable()->constrained('test_attempts')->onDelete('cascade')->after('test_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporary_answers', function (Blueprint $table) {
            $table->dropForeignIdFor('test_attempts', 'test_attempt_id');
        });
    }
};
