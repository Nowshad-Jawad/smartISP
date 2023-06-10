<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplates extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sms_apis_id',
        'template',
        'type',
        'status'
    ];

    public function sms_api(){
        return $this->belongsTo(SmsApi::class, 'sms_apis_id');
    }
}
