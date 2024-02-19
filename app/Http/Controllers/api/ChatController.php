<?php

namespace App\Http\Controllers\api;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function newMessage(Request $request)
    {
        $user = auth()->user();
        $receiver_id = $request['userId'];
        $message = $request['message'];
        $newMessage = new Message();
        $newMessage->receiver_id = $receiver_id;
        $newMessage->sender_id = $user->id;
        $newMessage->content = $message;
        $newMessage->status = 'chua-xem';
        $newMessage->save();
        if ($newMessage) {
            broadcast(new NewMessage($newMessage));
            return response()->json($newMessage, 200);
        }
    }
    public function getMessages($userId)
    {
        $user = auth()->user();
        $receiver_id = $userId;
        $messages = Message::where(function ($query) use ($user, $receiver_id) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($user, $receiver_id) {
            $query->where('sender_id', $receiver_id)
                ->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();
        return response()->json(['messages' => $messages], 200);
    }
}
