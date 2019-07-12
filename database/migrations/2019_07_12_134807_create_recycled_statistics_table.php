<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecycledStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recycled_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id')->nullable();
            $table->enum('recyclable_type', ['bucket', 'box'])->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('month');
            $table->unsignedInteger('entering_warehouse_amount')->nullable();
            $table->unsignedInteger('shipment_amount')->nullable();
            $table->unsignedInteger('recycled_amount')->nullable();
            $table->unsignedInteger('bad_amount')->nullable();
            $table->unsignedInteger('good_amount')->nullable();
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
        Schema::dropIfExists('recycled_statistics');
    }
}
