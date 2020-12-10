<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_tables', function (Blueprint $table) {
            $table->id();
            $table->string('userid');
            $table->integer('networkid');
            $table->string('amount');
            $table->string('incentive')->nullable();
            $table->string('commission')->nullable();
            $table->string('referral_bonus')->nullable();
            $table->string('msisdn');
            $table->string('et');
            $table->text('response')->nullable();
            $table->string('sessionid')->nullable();
            $table->string('pin');
            $table->integer('status');
            $table->string('serial');
            $table->string('product_code');
            $table->string('response_code');
            $table->string('ref_tag');
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
        Schema::dropIfExists('transaction_tables');
    }
}
