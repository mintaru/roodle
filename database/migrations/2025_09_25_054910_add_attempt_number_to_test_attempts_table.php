<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            // Добавляем новое поле attempt_number типа integer, без null, с дефолтным значением 1 (если нужно)
            $table->integer('attempt_number')->default(1)->after('id');
        });
    }
    
    public function down()
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropColumn('attempt_number');
        });
    }
    
};
