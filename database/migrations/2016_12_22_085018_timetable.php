<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Timetable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timetable', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course')->index();
            $table->string('subject')->default('')->index();
            $table->string('student')->default('')->index();
            $table->string('teacher')->default('')->index();
            $table->integer('index')->index();
            $table->date('start')->index();
            $table->date('end')->index();
            $table->tinyInteger('status')->default(1)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('timetable');
    }
}
