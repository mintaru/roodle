<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('test_question', function (Blueprint $table) {
            $table->unsignedInteger('page_number')
                ->default(1)
                ->after('question_order');
        });
    }

    public function down(): void
    {
        Schema::table('test_question', function (Blueprint $table) {
            $table->dropColumn('page_number');
        });
    }
};

