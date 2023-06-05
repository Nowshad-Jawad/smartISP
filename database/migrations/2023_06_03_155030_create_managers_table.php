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
        Schema::create('managers', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('password');
            $table->foreignId('zone_id')->refers('id')->on('zones');
            $table->foreignId('sub_zone_id')->refers('id')->on('sub_zones');
            $table->string('address');
            $table->integer('grace_allowed')->nullable();
            $table->boolean('prefix')->default(false);
            $table->string('prefix_text')->nullable();
            $table->foreignId('mikrotik_id')->refers('id')->on('mikrotiks');
            $table->foreignId('package_id')->refers('id')->on('packages')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};
