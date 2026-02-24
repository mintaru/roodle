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
            // Помечает, активен ли этот ответ (доступен ли для редактирования в текущей попытке)
            $table->boolean('is_active')->default(true)->after('answer_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporary_answers', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
