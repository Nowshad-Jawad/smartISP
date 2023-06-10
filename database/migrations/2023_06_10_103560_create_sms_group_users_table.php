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
        Schema::create('sms_group_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('smsgroup_id')->refers('id')->on('sms_groups')->onDelete('cascade')->nullable();
            $table->foreignId('customer_id')->refers('id')->on('customers')->onDelete('cascade')->nullable();
            $table->foreignId('manager_id')->refers('id')->on('managers')->onDelete('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_group_users');
    }
};
