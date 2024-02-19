<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foods extends Model
{
    use HasFactory;
    protected $table = 'food'; 
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'quantity',
        'expiry_date',
        'category_id',
        'address_id',
        'price',
        'status',
        'delivery_fee',
        'collect_type',
        'slug',
        'food_type',
        'operating_hours',
        'payment_methods',
        'remaining_time_to_accept',
        'created_at',
        'updated_at'
    ];
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
    public function category()
    {
        return $this->belongsTo(categories::class, 'category_id');
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
    public function foodTransactions()
    {
        return $this->hasMany(FoodTransactions::class, 'food_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rate::class, 'food_transaction_id');
    }
    public function images()
    {
        return $this->hasMany(ImagesFood::class, 'food_id');
    }
    public function detail_notification_subscribers()
    {
        return $this->hasMany(DetailNotificationSubscribers::class, 'food_id');
    }
}
