<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('shopify_id')->unsigned()->nullable();
            $table->string('email')->nullable();
            $table->longText('order_number')->nullable();
            $table->string('shipping_name')->nullable();
            $table->longText('address1')->nullable();
            $table->longText('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->text('phone')->nullable();
            $table->string('country')->nullable();
            $table->text('province')->nullable();
            $table->string('billing_shipping_name')->nullable();
            $table->longText('billing_address1')->nullable();
            $table->longText('billing_address2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_zip')->nullable();
            $table->text('billing_phone')->nullable();
            $table->string('billing_country')->nullable();
            $table->text('billing_province')->nullable();
            $table->text('financial_status')->nullable();
            $table->text('fulfillment_status')->nullable();
            $table->longText('fulfillments')->nullable();
            $table->longText('tags')->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->dateTime('shopify_created_at')->nullable();
            $table->dateTime('shopify_updated_at')->nullable();
            $table->string('total_price')->nullable();
            $table->longText('note')->nullable();
            $table->string('subtotal_price')->nullable();
            $table->string('total_weight')->nullable();
            $table->string('taxes_included')->nullable();
            $table->string('total_tax')->nullable();
            $table->string('currency')->nullable();
            $table->string('total_discounts')->nullable();
            $table->string('gateway')->nullable();
            $table->longText('refunds')->nullable();
            $table->bigInteger('shop_id')->unsigned()->nullable();
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
        Schema::dropIfExists('orders');
    }
}
