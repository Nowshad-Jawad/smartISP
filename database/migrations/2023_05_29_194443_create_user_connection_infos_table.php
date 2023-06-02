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
        Schema::create('user_connection_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->refers('id')->on('users');
            $table->foreignId('mikrotik_id')->refers('id')->on('mikrotiks');
            $table->string('username')->nullable();
            $table->string('user_password')->nullable();
            $table->string('service')->nullable();
            // $table->string('remote_address')->nullable();
            // $table->string('mac_address')->nullable();
            // $table->tinyInteger('mac_bind_status')->default(STATUS_SUCCESS);
            // $table->string('remote_ip')->nullable();
            // $table->string('router_component')->nullable();
            $table->string('expire_date')->nullable();
            // $table->tinyInteger('status')->default(STATUS_SUCCESS);
            // $table->boolean('is_queue')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_connection_infos');
    }
};
