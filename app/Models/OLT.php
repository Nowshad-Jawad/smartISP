<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OLT extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'zone_id',
        'sub_zone_id',
        'type',
        'non_of_pon_port',
        'management_ip',
        'management_vlan_id',
        'management_vlan_ip',
        'total_onu'
    ];

    public function zone(){
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}
