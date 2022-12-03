<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->timestamps();
            $table->softDeletes();

            $table->integer('total_amount')->unsigned()->comment('Total amount for the order');
            $table->integer('amount_to_pay')->unsigned()->default(0)->comment('Total amount to send to a payment processor');
            $table->integer('voucher_id')->unsigned()->nullable()->default(null)->comment('Voucher assigned to the order');
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
};
