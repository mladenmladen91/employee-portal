<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('name_short');
                $table->integer('zip_code');
                $table->bigInteger('country_id')->unsigned();
                $table->timestamps();

                $table->foreign('country_id')
                  ->references('id')
                  ->on('countries')
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
        Schema::dropIfExists('cities');
    }
}
