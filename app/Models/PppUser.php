<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PppUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_in_mkt',
        'mikrotik_id',
        'name',
        'service',
        'password',
        'profile',
        'localAddress',
        'remoteAddress',
        'onlyOne',
        'rateLimit',
        'dns',
        'status',
    ];

    public function mikrotik(){
        return $this->belongsTo(Mikrotik::class, 'mikrotik_id');
    }
}
