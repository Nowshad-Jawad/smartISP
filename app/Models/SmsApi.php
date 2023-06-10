<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsApi extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_url',
        'api_key',
        'sender_id',
        'client_id',
        'desc',
        'status'
    ];
}
