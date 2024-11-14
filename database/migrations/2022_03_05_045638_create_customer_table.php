<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('business',255);
            $table->string('name',50);
            $table->string('address',150);
            $table->string('state',150);
            $table->string('city',255);
            $table->string('zip_code',255);
            $table->string('telephone',255);
            $table->string('website',255);
            $table->string('email',255);
            $table->string('shop',255);
            $table->string('notes',255);
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
        Schema::dropIfExists('customer');
    }
}
