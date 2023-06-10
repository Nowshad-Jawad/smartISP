<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsGroupUsers extends Model
{
    use HasFactory;

    protected $fillable = [
        'smsgroup_id',
        'customer_id',
        'manager_id'
    ];

    public function user(){
        if($this->customer_id != null){
            return $this->belongsTo(Customer::class, 'customer_id');
        }

        if($this->manager_id != null){
            return $this->belongsTo(Manager::class, 'manager_id');
        }
    }
}
