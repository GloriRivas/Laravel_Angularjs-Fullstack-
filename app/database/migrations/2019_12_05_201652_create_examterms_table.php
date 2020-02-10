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
            $table->string('term')->nullable();
            $table->string('unitId')->nullable();
            $table->integer('unitestMarks');
            $table->integer('examMarks');
            $table->integer('classId');
            $table->integer('subjectId');
            $table->integer('examId');
            
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
