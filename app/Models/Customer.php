<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_in_mkt',
        'full_name',
        'email',
        'password',
        'gender',
        'national_id',
        'phone',
        'date_of_birth',
        'father_name',
        'mother_name',
        'address',
        'zone_id',
        'registration_date',
        'connection_date',
        'package_id',
        'bill',
        'discount',
        'mikrotik_id',
        'username',
        'pending'
    ];

    public function zone(){
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function package(){
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function mikrotik(){
        return $this->belongsTo(Mikrotik::class, 'mikrotik_id');
    }
}
