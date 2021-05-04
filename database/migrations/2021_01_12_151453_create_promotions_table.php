<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('gender');
            $table->string('country');
            $table->string('user_role');
            $table->integer('status')->default(0);
            $table->integer('is_send')->default(0);
            $table->string('title_en')->nullable();
            $table->string('subject_en')->nullable();
            $table->text('message_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('subject_ar')->nullable();
            $table->text('message_ar')->nullable();
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
        Schema::dropIfExists('promotions');
    }
}
