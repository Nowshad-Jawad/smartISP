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
        Schema::create('i_ppools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nas_type');
            $table->string('type');
            $table->string('start_ip');
            $table->string('end_ip');
            $table->string('subnet');
            $table->foreignId('mikrotik_id')->refers('id')->on('mikrotiks')->nullable();
            $table->longText('zoon')->nullable();
            $table->longText('franchise')->nullable();
            $table->longText('pop')->nullable();
            $table->string('total_number_of_ip');
            $table->string('public_ip')->nullable();
            $table->boolean('is_ip_charge')->default(0);
            $table->string('public_ip_charge')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('i_ppools');
    }
};
