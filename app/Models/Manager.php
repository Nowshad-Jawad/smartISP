<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'email',
        'phone',
        'password',
        'zone_id',
        'sub_zone_id',
        'address',
        'grace_allowed',
        'prefix',
        'prefix_text',
        'mikrotik_id'
    ];

    public function zone(){
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function sub_zone(){
        return $this->belongsTo(SubZone::class, 'sub_zone_id');
    }

    public function mikrotik(){
        return $this->belongsTo(Mikrotik::class, 'mikrotik_id');
    }
}
