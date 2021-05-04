<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_locations', function (Blueprint $table) {
            $table->id();
            $table->integer('course_id');
            $table->string('country')->nullable();
            $table->string('address')->nullable();
            $table->text('area')->nullable();
            $table->text('state')->nullable();
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
        Schema::dropIfExists('course_locations');
    }
}
