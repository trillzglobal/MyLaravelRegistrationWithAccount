<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinDataInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_data_infos', function (Blueprint $table) {
            $table->id();
            $table->string("userid");
            $table->string("email");
            $table->string("pin")->unique();
            $table->string("serial")->unique();
            $table->string("amount");
            $table->dateTime("time_purchased");
            $table->dateTime("time_expired")->nullable();
            $table->dateTime("time_used")->nullable();
            $table->integer("networkid")->nullable();
            $table->integer("status");
            $table->integer("processing");
            $table->string("used_by")->nullable();
            $table->string("sessionid")->nullable();
            $table->string("remark")->nullable();
            $table->string("ref_tag");
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
        Schema::dropIfExists('pin_data_infos');
    }
}
