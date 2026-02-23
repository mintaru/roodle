<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Добавляем поддержку текстовых ответов в таблицу options
        Schema::table('options', function (Blueprint $table) {
            // Для текстовых ответов: игнорировать регистр при проверке
            $table->boolean('case_insensitive')->default(true)->after('is_correct');
        });

        // Добавляем поле для хранения текстового ответа в таблицу temporary_answers
        Schema::table('temporary_answers', function (Blueprint $table) {
            // Текстовый ответ ученика
            $table->text('answer_text')->nullable()->after('option_id');
        });
    }

    public function down(): void
    {
        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('case_insensitive');
        });

        Schema::table('temporary_answers', function (Blueprint $table) {
            $table->dropColumn('answer_text');
        });
    }
};
