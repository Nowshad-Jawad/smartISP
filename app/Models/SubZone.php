<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'zone_id',
    ];

    public function zone(){
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}
