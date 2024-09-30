<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSenderProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sender_profiles', function (Blueprint $table) {
            $table->id();
            $table->integer('setting_id')->nullable();
            $table->boolean('selected')->default(0);
            $table->longText('plusFriendId')->nullable();
            $table->longText('senderKey')->nullable();
            $table->longText('categoryCode')->nullable();
            $table->string('status')->nullable();
            $table->longText('statusName')->nullable();
            $table->string('kakaoStatus')->nullable();
            $table->string('kakaoStatusName')->nullable();
            $table->string('kakaoProfileStatus')->nullable();
            $table->string('kakaoProfileStatusName')->nullable();
            $table->string('profileSpamLevel')->nullable();
            $table->string('profileMessageSpamLevel')->nullable();
            $table->boolean('dormant')->nullable();
            $table->boolean('block')->nullable();
            $table->longText('alimtalk')->nullable();
            $table->longText('friendtalk')->nullable();
            $table->longText('createDate')->nullable();
            $table->boolean('initialUserRestriction')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sender_profiles');
    }
}
