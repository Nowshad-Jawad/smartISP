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
        Schema::create('mikrotiks', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('nas_id')->nullable();
            $table->string('identity');
            $table->ipAddress("host");
            $table->string('username');
            $table->string('password');
            $table->string('port');
            $table->string('status')->default('1');
            $table->string('address');
            $table->string('sitename');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotiks');
    }
};
