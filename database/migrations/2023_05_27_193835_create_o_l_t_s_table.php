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
        Schema::create('o_l_t_s', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('zone_id')->refers('id')->on('zones');
            $table->foreignId('sub_zone_id')->refers('id')->on('sub_zones');
            $table->string('type');
            $table->string('non_of_pon_port');
            $table->string('management_ip');
            $table->string('management_vlan_ip');
            $table->string('management_vlan_id');
            $table->string('total_onu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_l_t_s');
    }
};
