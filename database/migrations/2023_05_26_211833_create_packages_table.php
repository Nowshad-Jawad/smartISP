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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('bandwidth')->nullable();
            $table->string('synonym')->nullable();
            $table->unsignedBigInteger('nas_id')->nullable();
            $table->unsignedBigInteger('mikrotik_id')->nullable();
            $table->unsignedBigInteger('queue_id')->nullable();
            $table->foreign('mikrotik_id')->references('id')->on('mikrotiks')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('pool_id')->nullable();
            $table->string('price')->nullable();
            $table->string('pop_price')->nullable();
            $table->string('franchise_price')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('manager_id')->default(1);
            $table->string('speed_unit')->nullable();
            $table->integer('uploadspeed')->nullable();
            $table->integer('downloadspeed')->nullable();
            $table->integer('numberofdevices')->nullable();
            $table->string('quota')->nullable();
            $table->string('users')->default('hotspot');
            $table->string('packagezone')->nullable();
            $table->integer('validdays')->nullable();
            $table->string('durationmeasure')->nullable();
            $table->longText('comment')->nullable();
            $table->string('local_address')->nullable();
            $table->string('fixed_expire_time')->nullable();
            $table->boolean('fixed_expire_time_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
