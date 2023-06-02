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
        Schema::table('ppp_users', function (Blueprint $table) {
            $table->boolean('added_in_customers_table')->after('id_in_mkt')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppp_users', function (Blueprint $table) {
            $table->dropColumn('added_in_customers_table');
        });
    }
};
