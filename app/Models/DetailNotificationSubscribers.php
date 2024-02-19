<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailNotificationSubscribers extends Model
{
    use HasFactory;
    protected $table = 'detail_notification_subscribers';
    protected $fillable = [
        'notification_subscriber_id',
        'sub_id',
        'food_id',
        'message',
        'user_image',
        'is_read',
        'type'
    ];

    public function notification_subscribers()
    {
        return $this->belongsTo(NotificationSubscribers::class, 'sub_id');
    }
    public function food()
    {
        return $this->belongsTo(Foods::class, 'food_id');
    }
}
