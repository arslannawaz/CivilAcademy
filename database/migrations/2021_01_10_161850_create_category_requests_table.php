<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_requests', function (Blueprint $table) {
            $table->id();
            $table->text('image');
            $table->string('title_en');
            $table->string('title_ar');
            $table->integer('trainer_id');
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('category_requests');
    }
}
