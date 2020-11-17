<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_responses', function (Blueprint $table) {
            $table->id();
            $table->string('amount');
            $table->string('userid');
            $table->string('email')->nullable();
            $table->date('paidon');
            $table->string('status');
            $table->text('response')->nullable();
            $table->string('method')->nullable();
            $table->string('exchanger_reference')->nullable();
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
        Schema::dropIfExists('webhook_responses');
    }
}
