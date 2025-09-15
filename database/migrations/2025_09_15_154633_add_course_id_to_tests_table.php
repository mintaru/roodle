<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            // Добавляем колонку для связи с курсами
            $table->foreignId('course_id')
                ->nullable()          // можно убрать nullable, если каждый тест должен быть привязан к курсу
                ->constrained('courses') // внешний ключ на таблицу courses
                ->onDelete('cascade');   // при удалении курса тесты будут удаляться
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });
    }
};
