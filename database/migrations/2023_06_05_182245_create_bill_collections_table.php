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
        Schema::create('bill_collections', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->foreignId('customer_id')->refers('id')->on('customers');
            $table->string('invoice_no');
            $table->string('method');
            $table->string('monthly_bill');
            $table->string('received_amount');
            $table->foreignId('manager_id')->refers('id')->on('managers');
            $table->string('transaction_id')->nullable();
            $table->string('issue_date');
            $table->string('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_collections');
    }
};
