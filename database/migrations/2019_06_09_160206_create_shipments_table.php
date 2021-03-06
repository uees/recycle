<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('recyclable_type', ['bucket', 'box'])->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('created_user_id')->nullable();
            $table->string('product_name', 128);
            $table->string('product_batch', 64)->index();
			$table->string('spec', 64)->nullable();
            $table->decimal('weight');
            $table->decimal('amount')->nullable();
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
        Schema::dropIfExists('shipments');
    }
}
