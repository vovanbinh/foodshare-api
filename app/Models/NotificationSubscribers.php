<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class NotificationSubscribers extends Model
{
    use HasFactory;
    protected $table = 'notification_subscribers';
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'notification_type',
    ];
    public function receiver()
    {
        return $this->belongsTo(Users::class, 'receiver_id');
    }
}
