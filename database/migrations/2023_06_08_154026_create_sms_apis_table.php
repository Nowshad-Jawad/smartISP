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
        Schema::create('sms_apis', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //Brillent, Reve System, 
            $table->longText('api_url')->nullable();
            $table->longText('api_key')->nullable();
            $table->longText('sender_id')->nullable(); //Sender Id or Secret key
            $table->longText('client_id')->nullable(); //Client Id or Caller ID
            $table->longText('desc')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_apis');
    }
};
