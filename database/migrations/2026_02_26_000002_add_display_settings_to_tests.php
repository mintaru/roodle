<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->boolean('randomize_questions')
                ->default(false)
                ->after('period_end');

            $table->string('display_mode', 32)
                ->default('single_page')
                ->after('randomize_questions');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['randomize_questions', 'display_mode']);
        });
    }
};

