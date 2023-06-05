<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'category_id',
        'amount',
        'method',
        'transaction_id',
        'date',
        'description',
        'manager_id',
        'vouchar_no'
    ];

    public function manager(){
        return $this->belongsTo(Manager::class, 'manager_id');
    }

    public function category(){
        return $this->belongsTo(AccountCategory::class, 'category_id');
    }
}
