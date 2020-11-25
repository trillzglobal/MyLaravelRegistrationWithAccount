<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePinBucketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_buckets', function (Blueprint $table) {
            $table->increments("serial")->unique();
            $table->string("pin")->unique();
            $table->string("value")->nullable();
            $table->string("bought_by")->nullable();
            $table->integer("status")->nullable();
            $table->dateTime("time_bought")->nullable();
            $table->string("ref_tag")->nullable();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE pin_buckets AUTO_INCREMENT = 548473928;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pin_buckets');
    }
}
