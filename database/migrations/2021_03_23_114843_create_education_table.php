<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('education_level_id')->unsigned()->nullable();
            $table->bigInteger('education_area_id')->unsigned();
            $table->bigInteger('education_title_id')->unsigned();
            $table->string('institution');
            $table->string('course');
            // $table->string('city');
            $table->string('city_id')->unsigned()->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('ongoing')->default(false);
            $table->timestamps();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('modified_by')->unsigned()->nullable();

            $table->foreign('created_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            $table->foreign('modified_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('education_level_id')
                ->references('id')
                ->on('education_levels')
                ->onDelete('cascade');
            $table->foreign('education_area_id')
                ->references('id')
                ->on('education_areas')
                ->onDelete('cascade');
            $table->foreign('education_title_id')
                ->references('id')
                ->on('education_titles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('education');
    }
}
