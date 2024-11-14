<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRingbuilderConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ringbuilder_config', function (Blueprint $table) {
            $table->id();
            $table->string('shop',255)->nullable();;
            $table->string('store_id',20)->nullable();;
            $table->string('store_location_id',20)->nullable();;
            $table->string('dealerid',6)->nullable();;
            $table->string('dealerpassword',255)->nullable();;
            $table->string('from_email_address',255)->nullable();;
            $table->string('admin_email_address',255)->nullable();;
            $table->string('dealerauthapi',255)->nullable();;
            $table->string('ringfiltersapi',255)->nullable();;
            $table->string('mountinglistapi',255)->nullable();;
            $table->string('mountinglistapifancy',255)->nullable();;
            $table->string('ringstylesettingapi',255)->nullable();;
            $table->string('navigationapi',255)->nullable();;
            $table->string('filterapi',255)->nullable();;
            $table->string('filterapifancy',255)->nullable();;
            $table->string('diamondlistapi',255)->nullable();;
            $table->string('diamondlistapifancy',255)->nullable();;
            $table->string('diamondshapeapi',255)->nullable();;
            $table->string('diamonddetailapi',255)->nullable();;
            $table->string('stylesettingapi',255)->nullable();;
            $table->char('enable_hint',10)->nullable();;
            $table->char('enable_email_friend',10)->nullable();;
            $table->char('enable_schedule_viewing',10)->nullable();;
            $table->char('enable_more_info',10)->nullable();;
            $table->char('enable_print',10)->nullable();;
            $table->char('enable_admin_notification',10)->nullable();;
            $table->char('default_viewmode',10)->nullable();;
            $table->char('show_filter_info',10)->nullable();;
            $table->char('show_powered_by',10)->nullable();;
            $table->char('enable_sticky_header',10)->nullable();;
            $table->string('settings_carat_ranges',500)->nullable();;
            $table->tinyInteger('display_tryon')->default('0');
            $table->string('shop_access_token',255)->nullable();;
            $table->string('shop_logo',255)->nullable();;
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
        Schema::dropIfExists('ringbuilder_config');
    }
}
