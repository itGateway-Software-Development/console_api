<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOperationSystemServerTypePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_system_server_type', function (Blueprint $table) {
            $table->unsignedBigInteger('operation_system_id')->index();
            $table->foreign('operation_system_id')->references('id')->on('operation_systems')->onDelete('cascade');
            $table->unsignedBigInteger('server_type_id')->index();
            $table->foreign('server_type_id')->references('id')->on('server_types')->onDelete('cascade');
            $table->primary(['operation_system_id', 'server_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_system_server_type');
    }
}
