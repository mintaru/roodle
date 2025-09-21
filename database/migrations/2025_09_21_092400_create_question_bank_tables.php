<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Таблица тестов
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('course_id')
                ->nullable()               // можно сделать not nullable, если каждый тест всегда привязан к курсу
                ->constrained('courses')  // связь с таблицей courses
                ->onDelete('cascade');    // при удалении курса тесты удаляются
            $table->timestamps();
        });

        // Банк вопросов
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->string('question_type')->default('single_choice'); // single_choice / multiple_choice
            $table->timestamps();
        });

        // Варианты ответов
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // Связь тест ↔ вопрос (многие-ко-многим)
        Schema::create('test_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('question_order')->nullable();
            $table->timestamps();
        });

        // Временные ответы пользователей
        Schema::create('temporary_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('test_id')->constrained('tests')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('option_id')->constrained('options')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temporary_answers');
        Schema::dropIfExists('test_question');
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('tests');
    }
};
