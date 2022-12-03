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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->string('unique_code', 16)->unique()->comment('Unique code');
            $table->integer('amount')->unsigned()->comment('Discount amount in cents');
            $table->dateTime('expired_dt')->comment('Expiration date time in UTC');
            $table->dateTime('used_dt')->nullable()->default(null)->comment('When the voucher has been used');

            $table->index(['expired_dt', 'used_dt']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
    }
};
