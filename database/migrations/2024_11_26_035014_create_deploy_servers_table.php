<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deploy_servers', function (Blueprint $table) {
            $table->id();
            $table->string('vm_id');
            $table->string('server_name');
            $table->string('server_type');
            $table->string('ip');
            $table->string('password');
            $table->string('server_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deploy_servers');
    }
};
