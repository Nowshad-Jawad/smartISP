<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConnectionInfo extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function mikrotik()
    {
        return $this->belongsTo(Mikrotik::class, 'mikrotik_id', 'id')->withDefault();
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
}
