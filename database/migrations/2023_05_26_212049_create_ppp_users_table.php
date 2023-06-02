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
        Schema::create('ppp_users', function (Blueprint $table) {
            $table->id();
            $table->string('id_in_mkt');
            $table->bigInteger('mikrotik_id')->unsigned();
            $table->foreign('mikrotik_id')->references('id')->on('mikrotiks');
            $table->string('name');
            $table->string('service');
            $table->string('password');
            $table->string('profile');
            $table->string('localAddress')->nullable();
            $table->string('remoteAddress')->nullable();
            $table->string('onlyOne')->nullable();
            $table->string('rateLimit')->nullable();
            $table->string('dns')->nullable();
            $table->string('status')->nullable();
            $table->tinyInteger('user_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppp_users');
    }
};
