<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdWorkTypeWorkTimeAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->bigInteger('job_type_id')->unsigned()->nullable();
            $table->bigInteger('work_time_id')->unsigned()->nullable();
            $table->foreign('job_type_id')
                    ->references('id')
                    ->on('job_types');
            $table->foreign('work_time_id')
                ->references('id')
                ->on('work_times');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ads', function (Blueprint $table) {
            //
        });
    }
}
