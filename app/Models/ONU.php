<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ONU extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mac',
        'olt_id',
        'pon_port',
        'onu_id',
        'rx_power',
        'distance',
        'vlan_tagged',
        'vlan_id',
        'user_id',
        'zone_id',
        'status',
    ];
}
