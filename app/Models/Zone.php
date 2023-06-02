<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
        'status'
    ];

    public function sub_zone(){
        return $this->hasMany(SubZone::class, 'id');
    }
}
