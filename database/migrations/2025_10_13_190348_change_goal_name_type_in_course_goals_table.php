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
    Schema::table('course_goals', function (Blueprint $table) {
        $table->text('goal_name')->change();
    });
}

public function down()
{
    Schema::table('course_goals', function (Blueprint $table) {
        $table->integer('goal_name')->change();
    });
}

};
