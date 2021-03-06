<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQcRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('recycled_thing_id')->nullable();
            $table->decimal('bad_amount')->default(0);
            $table->enum('recyclable_type', ['bucket', 'box'])->nullable();
            $table->enum('type', ['IQC', 'SC'])->default('IQC');
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
        Schema::dropIfExists('qc_records');
    }
}
