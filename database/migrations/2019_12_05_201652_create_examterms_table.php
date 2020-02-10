<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamtermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('examterms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('term',250);
            $table->integer('examId',250);
            $table->integer('classId',250);
            $table->integer('subjectId',250);
            $table->integer('unitestMarks',250);
            $table->integer('examMarks',250);
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
        Schema::drop('examterms');
    }
}
