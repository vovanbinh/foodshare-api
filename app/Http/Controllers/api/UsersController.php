<?php

namespace App\Http\Controllers\api;

use App\Models\DetailNotificationSubscribers;
use App\Models\Foods;
use App\Models\notification_subscribers;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditProficeRequest;
use App\Http\Requests\NewAvatarRequest;
use App\Http\Requests\NewPasswordRequest;
use App\Models\detail_notification_subscribers;
use App\Models\Food;
use App\Models\Notification;
use App\Models\NotificationSubscribers;
use App\Models\Users as ModelsUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function editProfice(EditProficeRequest $request)
    {
        $birthdate = Carbon::parse($request->input('birthdate'));
        $currentTime = Carbon::now();
        if ($birthdate < $currentTime) {
            $user = auth()->user();
            $user = ModelsUsers::where('id', $user->id)->first();
            $user->full_name = $request->input('full_name');
            $user->phone_number = $request->input('phone_number');
            $user->birthdate = $birthdate;
            $user->save();
            return response()->json(['message' => 'Cập nhật thành công'], 200);
        } else {
            return response()->json(['errors' => 'Ngày sinh phải trước ngày hiện tại'], 422);
        }
    }
    public function getProfice()
    {
        $user = auth()->user();
        $user = ModelsUsers::where('id', $user->id)->first();
        return response()->json(['user' => $user], 200);
    }
    public function newAvatar(NewAvatarRequest $request)
    {
        $user = auth()->user();
        $user = ModelsUsers::where('id', $user->id)->first();
        if ($user) {
            if ($request->hasFile('image')) {
                if ($user->image != null) {
                    $oldImagePath = $user->image;
                    $parts = explode('/', $oldImagePath);
                    $filename = end($parts);
                    $oldImagePath = public_path('uploads/user/' . $filename);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $image = $request->file('image');
                $file_name = Str::slug($user->full_name) . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/user'), $file_name);
                $user->image = '../../uploads/user/' . $file_name;
                $user->save();
                return response()->json(['message' => 'Cập nhật ảnh đại diện thành công'], 200);
            }
        }
    }
    public function newPassword(NewPasswordRequest $request)
    {
        $oldpassword = $request->input('old_password');
        $userTemp = auth()->user();
        $user = ModelsUsers::where('id', $userTemp->id)->first();

        if ($user && Hash::check($oldpassword, $user->password)) {
            $password = $request->input('password');
            if (Hash::check($password, $user->password)) {
                return response()->json(['error' => 'Mật Khẩu Mới Không Được Giống Mật Khẩu Cũ'], 422);
            }
            $hashedPassword = Hash::make($password);
            if ($user) {
                $user->password = $hashedPassword;
            }
            if ($user->save()) {
                return response()->json(['message' => 'Thay đổi mật khẩu thành công'], 200);
            }
        } else {
            return response()->json(['errors' => ['Mật Khẩu Cũ Không Đúng']], 422);
        }
    }
    public function getCountNotication(Request $request)
    {
        $user = auth()->user();
        $user = ModelsUsers::where('id', $user->id)->first();
        if ($user) {
            $perPage = $request->input('_limit', 8);
            $page = $request->input('_page', 1);
            $type = $request->input('type');
            $notificationCount = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
            $notificationSubCount = DetailNotificationSubscribers::where('notification_subscriber_id', $user->id)
                ->where('is_read', false)
                ->count();
            $notifications = Notification::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->with('transaction');
            $searchContent = $request->input('searchContent');
            if ($request->has('searchContent') && $searchContent !== null) {
                session(['searchContent' => $searchContent]);
                $notifications->where(function ($q) use ($searchContent) {
                    $q->where('notifications.message', 'like', '%' . $searchContent . '%');
                });
            }
            if ($type != null) {
                $notifications->where('is_read', $type);
            }
            $notifications = $notifications->paginate($perPage, ['*'], 'page', $page);
            return response()->json([
                'notificationCount' => $notificationCount + $notificationSubCount,
                'notifications' => $notifications
            ]);
        }
        return response()->json(['notificationCount' => 0]);
    }
    public function getNoticeDonatedFoods(Request $request)
    {
        $user = auth()->user();
        $user = ModelsUsers::where('id', $user->id)->first();
        if ($user) {
            $perPage = $request->input('_limit', 8);
            $page = $request->input('_page', 1);
            $type = $request->input('type');
            $notifications = DetailNotificationSubscribers::where('notification_subscriber_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->with('food');
            $searchContent = $request->input('searchContent');
            if ($request->has('searchContent') && $searchContent !== null) {
                session(['searchContent' => $searchContent]);
                $notifications->where(function ($q) use ($searchContent) {
                    $q->where('detail_notification_subscribers.message', 'like', '%' . $searchContent . '%');
                });
            }
            if ($type != null) {
                $notifications->where('is_read', $type);
            }
            $notifications = $notifications->paginate($perPage, ['*'], 'page', $page);
            return response()->json([
                'notifications' => $notifications
            ]);
        }
        return response()->json(null, 200);
    }
    public function notificationSubscribers(Request $request)
    {
        $user = auth()->user();
        $user = ModelsUsers::where('id', $user->id)->first();
        if ($user) {
            $foodId = $request['food_id'];
            $food = Foods::where('id', $foodId)->first();
            if ($food) {
                $senderId = $food->user_id;
                $existingSubscription = NotificationSubscribers::where('sender_id', $senderId)
                    ->where('receiver_id', $user->id)
                    ->first();
                if ($existingSubscription) {
                    $existingSubscription->notification_type = $request['new_value'];
                    $existingSubscription->save();
                    return response()->json('Đã cập nhật thành công');
                } else {
                    $sub_notification = new NotificationSubscribers();
                    $sub_notification->sender_id = $senderId;
                    $sub_notification->receiver_id = $user->id;
                    $sub_notification->notification_type = $request['new_value'];
                    $sub_notification->save();
                    if ($sub_notification) {
                        return response()->json('Đăng kí mới thành công');
                    }
                }
            } else {
                return response()->json('Không tồn tại food');
            }
        }
    }

    public function getTotalNoticeTransaction()
    {
        $user = auth()->user();
        $user = ModelsUsers::where('id', $user->id)->first();
        $notificationCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        return response()->json($notificationCount);
    }
    public function getTotalNoticeSub()
    {
        $user = auth()->user();
        $user = ModelsUsers::where('id', $user->id)->first();
        $notificationSubCount = DetailNotificationSubscribers::where('notification_subscriber_id', $user->id)
            ->where('is_read', false)
            ->count();
        return response()->json($notificationSubCount);
    }

    public function getPublicProfice($userId){
        $user = ModelsUsers::select('full_name', 'email','image','bio')->where('id', $userId)->first();
        return response()->json($user, 200);
    }
}
