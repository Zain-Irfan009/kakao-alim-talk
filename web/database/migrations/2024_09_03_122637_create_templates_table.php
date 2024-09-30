<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->nullable();
            $table->integer('setting_id')->nullable();
            $table->integer('sender_profile_id')->nullable();
            $table->longText('plusFriendId')->nullable();
            $table->longText('senderKey')->nullable();
            $table->string('plusFriendType')->nullable();
            $table->string('templateCode')->nullable();
            $table->longText('kakaoTemplateCode')->nullable();
            $table->longText('templateName')->nullable();
            $table->string('templateMessageType')->nullable();
            $table->string('templateEmphasizeType')->nullable();
            $table->longText('templateContent')->nullable();
            $table->longText('templateExtra')->nullable();
            $table->longText('templateAd')->nullable();
            $table->longText('templateTitle')->nullable();
            $table->longText('templateSubtitle')->nullable();
            $table->longText('templateHeader')->nullable();
            $table->longText('templateItem')->nullable();
            $table->longText('templateItemHighlight')->nullable();
            $table->longText('templateRepresentLink')->nullable();
            $table->longText('templateImageName')->nullable();
            $table->longText('templateImageUrl')->nullable();
            $table->longText('buttons')->nullable();
            $table->longText('quickReplies')->nullable();
            $table->longText('comments')->nullable();
            $table->string('status')->nullable();
            $table->string('statusName')->nullable();
            $table->boolean('securityFlag')->nullable();
            $table->string('categoryCode')->nullable();
            $table->longText('createDate')->nullable();
            $table->longText('updateDate')->nullable();
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
        Schema::dropIfExists('templates');
    }
}
