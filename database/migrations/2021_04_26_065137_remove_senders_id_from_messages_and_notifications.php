<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSendersIdFromMessagesAndNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropColumn(['sender_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropColumn(['sender_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            //
        });

        Schema::table('message', function (Blueprint $table) {
            //
        });
    }
}
