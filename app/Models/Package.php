<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'bandwidth',
        'synonym',
        'nas_id',
        'mikrotik_id',
        'queue_id',
        'pool_id',
        'price',
        'pop_price',
        'franchise_price',
        'status',
        'manager_id',
        'speed_unit',
        'uploadspeed',
        'downloadspeed',
        'numberofdevices',
        'quota',
        'users',
        'packagezone',
        'validdays',
        'durationmeasure',
        'comment',
        'local_address',
        'fixed_expire_time',
        'fixed_expire_time_status'    
    ];

    public function mikrotik()
    {
        return $this->belongsTo(Mikrotik::class, 'mikrotik_id');
    }
}
