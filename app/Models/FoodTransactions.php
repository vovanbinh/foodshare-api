<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodTransactions extends Model
{
    use HasFactory;
    protected $table = 'food_transactions';
    protected $fillable = [
        'food_id',
        'receiver_id',
        'quantity_received',
        'pickup_time',
        'status',
        'anonymous',
        //0 là mặc đinh, 1 là ẩn danh
        'receiver_status',
        'is_error_notification',
        'donor_status',
    ];
    public function food()
    {
        return $this->belongsTo(Foods::class, 'food_id');
    }

    // Định nghĩa mối quan hệ với bảng users cho người nhận
    public function receiver()
    {
        return $this->belongsTo(Users::class, 'receiver_id');
    }

    public function ratings()
    {
        return $this->hasMany(rate::class, 'food_transaction_id', 'id');
    }
}
