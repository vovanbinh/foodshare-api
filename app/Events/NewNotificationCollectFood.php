<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotificationCollectFood implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userIds;

    public function __construct($userIds)
    {
        $this->userIds = $userIds;
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->userIds);
    }

    public function broadcastWith()
    {
        return ['text' => 'Bạn vừa có một thông báo mới'];
    }
}
