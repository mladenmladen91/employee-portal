<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagePurchaseHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_purchase_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('package_id')->unsigned();
            $table->boolean('purchase_auto')->default(false);
            $table->timestamp('purchase_date')->nullable();
            $table->timestamp('expire_date')->nullable();
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
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
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
        Schema::dropIfExists('package_purchase_histories');
    }
}
