<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'message',
        'user_image',
        'foodId',
        'is_read',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
    public function transaction()
    {
        return $this->belongsTo(FoodTransactions::class, 'transaction_id');
    }
    public function food()
    {
        return $this->belongsTo(Foods::class, 'foodId');
    }
}
