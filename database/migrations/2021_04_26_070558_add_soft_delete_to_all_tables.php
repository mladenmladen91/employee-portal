<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteToAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
      

        Schema::table('company_users', function (Blueprint $table) {
            $table->softDeletes();
            $table->bigInteger('deleted_by')->unsigned()->nullable();

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('ads', function (Blueprint $table) {
            $table->softDeletes();
            $table->bigInteger('deleted_by')->unsigned()->nullable();

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('ad_shared_infos', function (Blueprint $table) {
            $table->softDeletes();
            $table->bigInteger('deleted_by')->unsigned()->nullable();

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('ad_answers', function (Blueprint $table) {
            $table->softDeletes();
            $table->bigInteger('deleted_by')->unsigned()->nullable();

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('ad_questions', function (Blueprint $table) {
            $table->softDeletes();
            $table->bigInteger('deleted_by')->unsigned()->nullable();

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('predefineds', function (Blueprint $table) {
            $table->softDeletes();
            $table->bigInteger('deleted_by')->unsigned()->nullable();

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->softDeletes();
            $table->bigInteger('deleted_by')->unsigned()->nullable();

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('company_users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ads', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ad_shared_infos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ad_answers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('ad_questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('predefineds', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('packages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
