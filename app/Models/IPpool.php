<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPpool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nas_type',
        'type',
        'start_ip',
        'end_ip',
        'subnet',
        'mikrotik_id',
        'total_number_of_ip',
        'public_ip',
        'is_ip_charge',
        'public_ip_charge',
        'status',
    ];

    public function mikrotik()
    {
        return $this->belongsTo(Mikrotik::class, 'mikrotik_id');
    }
}
