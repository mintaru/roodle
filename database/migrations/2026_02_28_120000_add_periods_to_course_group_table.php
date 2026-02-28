<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('course_group', function (Blueprint $table) {
            $table->datetime('period_start')->nullable()->after('group_id');
            $table->datetime('period_end')->nullable()->after('period_start');
        });
    }

    public function down(): void
    {
        Schema::table('course_group', function (Blueprint $table) {
            $table->dropColumn(['period_start', 'period_end']);
        });
    }
};
