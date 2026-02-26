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
            $table->boolean('is_manually_graded')
                ->default(false)
                ->after('is_active');

            $table->boolean('is_correct_manual')
                ->nullable()
                ->after('is_manually_graded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporary_answers', function (Blueprint $table) {
            $table->dropColumn(['is_manually_graded', 'is_correct_manual']);
        });
    }
};

