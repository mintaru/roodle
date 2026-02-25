<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Синхронизируем обработку часовых поясов для колонок started_at и ended_at
     * Обе колонки теперь будут использовать одинаковую TIMESTAMP логику БД
     */
    public function up(): void
    {
        // Переопределяем обе колонки чтобы гарантировать одинаковую обработку
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->timestamp('started_at')->change();
            $table->timestamp('ended_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Отмена изменений
    }
};
