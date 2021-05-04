<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()

    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->float('price')->default(0.0);
            $table->integer('category_id');
            $table->boolean('status')->default(true);
            $table->text('logo')->nullable();
            $table->string('organized_by')->nullable();

            $table->string('title_en');
            $table->string('title_ar')->nullable();

            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();

            $table->text('document_en')->nullable();
            $table->text('document_ar')->nullable();

            $table->text('registeration_procedure_en')->nullable();
            $table->text('registeration_procedure_ar')->nullable();

            $table->text('lecturer_detail_en')->nullable();
            $table->text('lecturer_detail_ar')->nullable();

            $table->text('fee_detail_en')->nullable();
            $table->text('fee_detail_ar')->nullable();

            $table->text('test_detail_en')->nullable();
            $table->text('test_detail_ar')->nullable();

            $table->text('payment_refund_policy_en')->nullable();
            $table->text('payment_refund_policy_ar')->nullable();

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
        Schema::dropIfExists('courses');
    }
}
