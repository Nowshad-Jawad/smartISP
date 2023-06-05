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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_for')->after('invoice_no');
            $table->string('paid_by')->after('last_payment_amount');
            $table->string('transaction_id')->after('paid_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_for');
            $table->dropColumn('paid_by');
            $table->dropColumn('transaction_id');
        });
    }
};
