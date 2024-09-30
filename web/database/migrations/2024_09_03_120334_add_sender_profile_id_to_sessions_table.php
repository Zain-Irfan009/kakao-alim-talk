<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSenderProfileIdToSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->integer('setting_id')->nullable()->default(null);
            $table->boolean('order_create_enable')->nullable()->default(1);
            $table->boolean('order_update_enable')->nullable()->default(1);
            $table->boolean('order_fulfill_enable')->nullable()->default(1);
            $table->boolean('order_cancel_enable')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            //
        });
    }
}
