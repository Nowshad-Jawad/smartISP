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
        Schema::create('user_billing_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->refers('id')->on('users');
            $table->foreignId('package_id')->refers('id')->on('packages');
            $table->unsignedInteger('purches_package_id')->nullable();
            $table->string('purches_package_name')->nullable();
            $table->foreignId('zone_id')->refers('id')->on('zones');
            $table->foreignId('sub_zone_id')->refers('id')->on('sub_zones');
            $table->decimal('monthly_bill', 8, 2);
            $table->decimal('discount', 8, 2)->default(0);
            $table->date('registration_date');
            $table->date('connection_date')->nullable();
            $table->string('connection_type')->nullable();
            $table->string('connection_fee')->nullable();
            $table->string('user_reference_number');
            $table->string('user_payment_type');
            $table->integer('status')->default(1);
            $table->boolean('sms_notification');
            $table->decimal('balance', 8, 2)->default(0);
            $table->boolean('email_notification');
            $table->boolean('is_home_collect')->default(0);
            $table->bigInteger('collect_by')->nullable();
            $table->string('last_payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_billing_infos');
    }
};
