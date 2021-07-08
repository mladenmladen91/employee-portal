<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_questions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ad_id')->unsigned();
            $table->text('text_question')->nullable();
            $table->string('video_question')->nullable();
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

            $table->foreign('ad_id')
                ->references('id')
                ->on('ads')
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
        Schema::dropIfExists('ad_questions');
    }
}
