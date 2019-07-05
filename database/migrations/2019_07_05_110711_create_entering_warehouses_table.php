<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnteringWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entering_warehouses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_name', 128);
            $table->string('product_batch', 64)->index();
            $table->string('spec', 64)->nullable();
            $table->decimal('weight');
            $table->decimal('amount')->nullable();
            $table->timestamp('entered_at')->nullable();  # 入库日期
            $table->timestamp('made_at')->nullable();  # 生产日期
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
        Schema::dropIfExists('entering_warehouses');
    }
}
