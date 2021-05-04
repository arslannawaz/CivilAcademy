<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('residence')->nullable();
            $table->string('organization')->nullable();
            $table->string('experience')->nullable();
            $table->text('trade_licence')->nullable();
            $table->text('trn_certifiate')->nullable();
            $table->text('certificates')->nullable();
            $table->text('emirate_id')->nullable();
            $table->string('course_offered')->nullable();
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
        Schema::dropIfExists('trainers');
    }
}
