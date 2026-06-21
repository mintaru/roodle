<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('course_id');
            $table->boolean('is_global')->default(false)->after('user_id');
            $table->foreignId('course_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'is_global']);
            $table->foreignId('course_id')->nullable(false)->change();
        });
    }
};
