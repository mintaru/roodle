<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_section_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')
                ->constrained('course_sections')
                ->onDelete('cascade');
            $table->foreignId('group_id')
                ->constrained('groups')
                ->onDelete('cascade');
            $table->timestamps();

            $table->unique(['course_section_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_section_groups');
    }
};
