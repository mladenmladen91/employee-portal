<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('type_of_work_id')->unsigned();
            $table->string('work_location');
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
            $table->foreign('type_of_work_id')
                ->references('id')
                ->on('type_of_works')
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
        Schema::dropIfExists('user_settings');
    }
}
