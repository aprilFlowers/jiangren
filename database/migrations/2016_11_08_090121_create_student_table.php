<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('userId')->index();
            $table->string('name')->index();
            $table->string('grade')->index();
            $table->string('phoneNum')->index();
            $table->text('baseInfos');
            $table->text('family');
            $table->tinyInteger('status')->default(1)->index();
            //$table->text('courseInfos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('students');
    }
}
