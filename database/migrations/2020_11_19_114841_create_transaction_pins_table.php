<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionPinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_pins', function (Blueprint $table) {
            $table->id();
            $table->string('userid');
            $table->string('email');
            $table->string('pin_value');
            $table->string('pin_amount');
            $table->string('pincount');
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
        Schema::dropIfExists('transaction_pins');
    }
}
