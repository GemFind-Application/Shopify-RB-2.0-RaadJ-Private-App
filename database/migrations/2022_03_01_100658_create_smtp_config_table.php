<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmtpConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smtp_config', function (Blueprint $table) {
            $table->increments('shopid');
            $table->string('protocol',255);
            $table->string('smtphost',150)->nullable();
            $table->tinyInteger('smtpport')->nullable();
            $table->string('smtpusername',150)->nullable();
            $table->string('smtppassword',150)->nullable();
            $table->string('shop_name',255);
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
        Schema::dropIfExists('smtp_config');
    }
}
