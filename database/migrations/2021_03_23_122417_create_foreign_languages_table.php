<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeignLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foreign_languages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('languages_id')->unsigned();
            $table->bigInteger('language_reads_id')->unsigned();
            $table->bigInteger('language_writes_id')->unsigned();
            $table->bigInteger('language_speaks_id')->unsigned();
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
            $table->foreign('languages_id')
                ->references('id')
                ->on('languages')
                ->onDelete('cascade');
            $table->foreign('language_reads_id')
                ->references('id')
                ->on('language_reads')
                ->onDelete('cascade');
            $table->foreign('language_writes_id')
                ->references('id')
                ->on('language_writes')
                ->onDelete('cascade');
            $table->foreign('language_speaks_id')
                ->references('id')
                ->on('language_speaks')
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
        Schema::dropIfExists('foreign_languages');
    }
}
