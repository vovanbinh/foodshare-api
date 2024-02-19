<?php

namespace App\Http\Controllers\api;

use App\Events\NewNotificationCollectFood;
use App\Http\Controllers\Controller;
use App\Http\Requests\CollectFoodRequest;
use App\Models\Address;
use App\Models\DetailNotificationSubscribers;
use App\Models\ImagesFood;
use Illuminate\Http\Request;
use App\Models\district;
use App\Models\Foods;
use App\Models\FoodTransactions;
use App\Models\Notification;
use App\Models\NotificationSubscribers;
use App\Models\province;
use App\Models\rate;
use App\Models\Users;
use App\Models\ward;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Mockery\Undefined;

class FoodTransactionsController extends Controller
{
    public function collectFood(CollectFoodRequest $request)
    {

        $user = auth()->user();
        $user = Users::where('id', $user->id)->first();
        $quantity = $request->value["Quantity"];
        $food = Foods::findOrFail($request->value["foodId"]);
        // $foodTransaction = FoodTransactions::where('food_id', $request->value["foodId"])
        //     ->where('receiver_id', $user->id)
        //     ->latest('created_at')
        //     ->first();

        // if ($foodTransaction) {
        //     $pickupTime = $foodTransaction->created_at;
        //     $currentTime = now();
        //     $timeDiff = $currentTime->diffInHours($pickupTime);
        //     if ($timeDiff < 4) {
        //         return response()->json(['errors' => 'Bạn đã nhận thực phẩm này trong vòng 4 giờ trước đó.'], 422);
        //     }
        // }
        $fourHoursAgo = Carbon::now()->subHours(4);
        $userCurrentTransactions = FoodTransactions::where('receiver_id', $user->id)->get();
        foreach ($userCurrentTransactions as $transaction) {
            if (Carbon::parse($transaction->created_at)->gt($fourHoursAgo) && $transaction->status == 1) {
                return response()->json(['errors' => 'Bạn đã nhận thực phẩm thành công trong vòng 4 giờ trước đó.'], 422);
            }
        }

        if ($food->quantity < $quantity) {
            return response()->json(['errors' => 'Số lượng không đủ'], 400);
        }
        $anonymous = isset($request->value["anonymous"]) ? ($request->value["anonymous"] ? 1 : 0) : 0;
        $foodTrans = new FoodTransactions([
            'food_id' => $request->value["foodId"],
            'receiver_id' => $user->id,
            'anonymous' => $anonymous,
            'quantity_received' => $request->value["Quantity"],
        ]);
        $food->quantity -= $quantity;
        $food->status = 1;
        $foodtemp = $food;
        try {
            DB::beginTransaction();
            if ($foodTrans->save() && $food->save()) {
                DB::commit();
                $user = $food->user;
                $notification = new Notification();
                $notification->transaction_id = $foodTrans->id;
                $notification->user_id = $user->id;
                $notification->foodId = null;
                $notification->type = 0;
                $user = auth()->user();
                $notification->user_image = $user->image;
                $notification->message = $user->full_name . ' muốn nhận ' . $food->title . ' với số lượng là: ' . $quantity . '. Bạn có chấp nhận không?'; // Note the corrected message string
                $notification->save();
                broadcast(new NewNotificationCollectFood($food->user->id));
                return response()->json(['message' => 'Nhận Thành Công, vui lòng đợi người tặng xác nhận'], 200);
            } else {
                DB::rollback();
                return response()->json(['errors' => 'Failed to update records'], 500);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e], 500);
        }
    }
    public function getTotalCart($userId)
    {
        $totalQuantity = FoodTransactions::where('receiver_id', $userId)
            ->sum('quantity_received');
        return response()->json(['total' => $totalQuantity], 201);
    }
    public function getReceivedList(request $request)
    {
        $user = auth()->user();
        $user = Users::where('id', $user->id)->first();
        $perPage = $request->input('_limit', 8);
        $page = $request->input('_page', 1);
        if ($user) {
            $received_food = FoodTransactions::where('receiver_id', $user->id)
                ->with(['food.user', 'ratings'])
                ->orderBy('created_at', 'desc')
                ->with(['food.user']);
            $received_food = $received_food->paginate($perPage, ['*'], 'page', $page);
            return response()->json(['received_list' => $received_food], 201);
        }
    }
    public function cancelReceived(Request $request)
    {
        $user = auth()->user();
        $foodTransaction = FoodTransactions::find($request[0]);
        if ($foodTransaction->receiver_id == $user->id) {
            if (!$foodTransaction) {
                return response()->json(['error' => 'Không tìm thấy giao dịch thực phẩm.'], 422);
            }
            $foodTransaction->status = 2;
            $foodTransaction->save();

            $food = $foodTransaction->food;
            $food->quantity += $foodTransaction->quantity_received;
            $food->save();
            return response()->json(['message' => 'Đã hủy nhận thực phẩm thành công.', 200]);
        } else {
            return response()->json(['errors' => 'Không có quyền hủy thực phẩm này.', 401]);
        }
    }
    public function historyTransactions(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('_limit', 8);
        $page = $request->input('_page', 1);
        $foodTransactions = FoodTransactions::whereHas('food', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['food', 'receiver', 'ratings'])->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        foreach ($foodTransactions as $foodTransaction) {
            $food = $foodTransaction->food;
            $imageUrls = ImagesFood::where('food_id', $food->id)->pluck('image_url')->toArray();
            $food->image_urls = $imageUrls;
        }

        return response()->json($foodTransactions);
    }

    public function confirmReceived(Request $request)
    {
        $user = auth()->user();
        $foodTransaction = FoodTransactions::find($request[0]);
        if (!$foodTransaction) {
            return response()->json(['errors' => 'Không tìm thấy giao dịch'], 422);
        }
        if ($foodTransaction->food->user_id !== $user->id) {
            return response()->json(['errors' => 'Không có quyền truy cập giao dịch'], 422);
        }
        if ($foodTransaction->status == 1) {
            return response()->json(['errors' => 'Đã xác nhận giao dịch'], 422);
        }
        $currentTime = now();
        $foodTransaction->update([
            'status' => 1,
            'pickup_time' => $currentTime,
        ]);
        $fourHoursAgo = Carbon::now()->subHours(4);
        $userReceiverTransactions = FoodTransactions::where('receiver_id', $foodTransaction->receiver_id)->get();
        foreach ($userReceiverTransactions as $transaction) {
            if (Carbon::parse($transaction->created_at)->gt($fourHoursAgo) && $transaction->status == 0) {
                $transaction->status = 2;
                $transaction->save();
                $food = $transaction->food;
                $food->quantity += $foodTransaction->quantity_received;
                $food->save();
            }
        }
        return response()->json(['message' => 'Xác Nhận Đã Lấy Thành Công'], 200);
    }

    public function notifiRefuse(Request $request)
    {
        $transaction = FoodTransactions::find($request[0]);
        $receiver_id = $transaction->receiver->id;
        $food = $transaction->food;
        $user = $food->user;
        $transaction->status = 2;
        $transaction->donor_status = 2;
        $transaction->save();
        $notification = new Notification();
        $notification->transaction_id = $transaction->id;
        $notification->type = 2;
        $notification->user_image = $user->image;
        $notification->user_id = $receiver_id;
        $notification->message = $user->full_name . ' đã từ chối tặng sản phẩm ' . $food->title . '. Vui lòng nhận thực phẩm khác bạn nhé!';
        $notification->save();
        $food = $transaction->food;
        $food->quantity += $transaction->quantity_received;
        $food->save();
        return response()->json(['message' => 'Xác nhận từ chối tặng thành công'], 200);
    }
    public function notifiConfirm(Request $request)
    {
        $transaction = FoodTransactions::find($request['transaction_id']);

        if (!$transaction) {
            return response()->json(['message' => 'Không tìm thấy giao dịch'], 404);
        }
        $existingNotification = Notification::find($request['notice_id']);
        if ($existingNotification) {
            $existingNotification->type = 1;
            $existingNotification->save();
        }
        $food = $transaction->food;
        if (!$food) {
            return response()->json(['message' => 'Không tìm thấy thông tin thực phẩm'], 404);
        }
        $user = $food->user;
        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy thông tin người dùng'], 404);
        }
        $receiver_id = $transaction->receiver->id;
        $transaction->donor_status = 1;
        $transaction->donor_confirm_time = now('Asia/Ho_Chi_Minh');
        $transaction->save();
        $notification = new Notification();
        $notification->user_image = $user->image;
        $notification->transaction_id = $transaction->id;
        $notification->type = 1;
        $notification->user_id = $receiver_id;
        $notification->message = $user->full_name . ' đã chấp nhận bạn tới lấy ' . $food->title . '. Vui lòng kiểm tra lại thời gian cho phép nhận thực phẩm để không bỏ lỡ thực phẩm bạn nhé!';
        $notification->save();
        return response()->json(['message' => 'Xác nhận đồng ý thành công'], 200);
    }


    public function notifiViewed(Request $request)
    {
        $notification = Notification::where('id', $request[0])->first();
        if ($notification) {
            $notification->is_read = 1;
            $notification->save();
        }
    }
    public function notifiViewedDonatedfood(Request $request)
    {
        $notification = DetailNotificationSubscribers::where('id', $request[0])->first();
        if ($notification) {
            $notification->is_read = 1;
            $notification->save();
        }
    }

    public function detailTransaction($transactionId)
    {
        $foodTransaction = FoodTransactions::find($transactionId);

        if (empty($foodTransaction)) {
            return response()->json(null, 200);
        }
        $food = Foods::find($foodTransaction->food_id);

        if (empty($food)) {
            return response()->json(null, 200);
        }
        $imageUrls = ImagesFood::where('food_id', $foodTransaction->food_id)->pluck('image_url')->toArray();
        $user = Users::find($food->user_id);
        $address = Address::find($food->address_id);
        $contact_information = $address->contact_information;
        $foodData = $food->toArray();
        $combinedData = array_merge(
            $foodData,
            [
                'imageUrls' => $imageUrls,
                'user' => $user,
                'formatted_address' => $address->formatted_address,
                'lon' => $address->lon,
                'lat' => $address->lat,
                'contact_information' => $contact_information,
            ]
        );

        $ratings = [];
        if ($food->foodTransactions) {
            foreach ($food->foodTransactions as $transaction) {
                $transactions[] = $transaction;
                $transactionRatings = rate::where('food_transaction_id', $transaction->id)
                    ->first();
                if (isset($transactionRatings)) {
                    $userRating = Users::find($transaction->receiver_id);
                    $ratings[$transaction->id] = ['rating' => $transactionRatings, 'user' => $userRating];
                } else {
                    $ratings[$transaction->id] = null;
                }
            }
        }
        $isSubscribed = false;
        if (auth()->user()) {
            $isSubscribed = NotificationSubscribers::where('sender_id', $food->user_id)
                ->where('receiver_id', auth()->id())
                ->where('notification_type', 1)
                ->exists();
        }
        return response()->json(['food' => $combinedData, 'ratings' => $ratings, 'transaction' => $foodTransaction, 'isSubscribed' => $isSubscribed]);
    }

    public function errorNotifications(Request $request)
    {
        $foodId = $request['foodId'];
        $message = $request['message'];
        $food = Foods::find($foodId);
        if (!$food) {
            return response()->json(['error' => 'Food not found'], 404);
        }
        $user = auth()->user();
        $transactions = $food->foodTransactions;
        $senderNotification = new Notification([
            'user_id' => $food->user_id,
            'foodId' => $food->id,
            'message' => $user->full_name . ' gửi thông báo: ' . $message,
            'image' => $user->image,
            'is_read' => 0,
            'type' => '4044', //type doner
        ]);
        $senderNotification->save();
        $senderNotification = new Notification([
            'user_id' => 120,
            'foodId' => $food->id,
            'message' => $user->full_name . ' gửi thông báo: ' . $message,
            'image' => $user->image,
            'is_read' => 0,
            'type' => '4045', //type admin
        ]);
        $senderNotification->save();
        foreach ($transactions as $transaction) {
            $receiver = $transaction->receiver;
            if ($receiver->id !== $food->user_id) {
                $notification = new Notification([
                    'user_id' => $receiver->id,
                    'transaction_id' => $transaction->id,
                    'message' => $user->full_name . ' gửi thông báo: ' . $message,
                    'image' => $user->image,
                    'is_read' => 0,
                    'type' => '404',
                ]);
                $notification->save();
            }
        }
        if ($notification) {
            $transaction = FoodTransactions::where('id', $request['transactionId'])->first();
            $transaction->is_error_notification = 1;
            $transaction->save();
            if ($transaction) {
                return response()->json(['success' => 'Gửi thông báo đến mọi người thành công'], 200);
            }
        }
    }
}
