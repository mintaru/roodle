<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lectures', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->change();
            $table->string('content_type')->default('text'); // 'text' | 'html'
        });
    }

    public function down(): void
    {
        Schema::table('lectures', function (Blueprint $table) {
            $table->string('pdf_path')->nullable(false)->change();
            $table->dropColumn('content_type');
        });
    }
};
