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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->refers('id')->on('users');
            $table->string('invoice_no');
            $table->unsignedBigInteger('package_id')->refers('id')->on('packages')->nullable();
            $table->unsignedBigInteger('zone_id')->refers('id')->on('zones')->nullable();
            $table->unsignedBigInteger('sub_zone_id')->refers('id')->on('sub_zones')->nullable();
            $table->timestamp('expire_date');
            $table->decimal('amount', 8, 2);
            $table->decimal('received_amount', 8, 2)->default(0);
            $table->decimal('due_amount', 8, 2)->default(0);
            $table->decimal('advanced_amount', 8, 2)->default(0);
            $table->dateTime('last_payment_date')->nullable();
            $table->decimal('last_payment_amount', 8, 2)->nullable();
            $table->tinyInteger('notification_status')->default(0);
            $table->string('status')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
