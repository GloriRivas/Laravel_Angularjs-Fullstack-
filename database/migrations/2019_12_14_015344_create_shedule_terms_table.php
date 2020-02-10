<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSheduleTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shedule_terms', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('allow');
            $table->integer('classId');
            $table->integer('subjectId');
            $table->integer('year');
            $table->integer('examId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shedule_terms');
    }
}
