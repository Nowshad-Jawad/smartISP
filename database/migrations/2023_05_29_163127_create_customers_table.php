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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('gender')->nullable();
            $table->string('national_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('zone_id')->refers('id')->on('zones')->nullable();
            $table->string('registration_date')->nullable();
            $table->string('connection_date')->nullable();
            $table->foreignId('package_id')->refers('id')->on('packages')->nullable();
            $table->string('bill')->nullable();
            $table->string('discount')->nullable();
            $table->foreignId('mikrotik_id')->refers('id')->on('mikrotiks')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('password')->nullable();
            $table->boolean('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
