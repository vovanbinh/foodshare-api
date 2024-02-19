<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DonateFoodRequest;
use App\Http\Requests\EditFoodDonatedRequest;
use App\Models\Address;
use App\Models\DetailNotificationSubscribers;
use App\Models\District;
use App\Models\Foods;
use App\Models\FoodTransactions;
use App\Models\ImagesFood;
use App\Models\Notification;
use App\Models\NotificationSubscribers;
use App\Models\province;
use App\Models\Rate;
use App\Models\Users;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class FoodController extends Controller
{
    public function getDetail($foodSlug)
    {
        $currentDateTime = Carbon::now();
        $userCurrent = null;
        if (auth()->user()) {
            $userCurrent = auth()->user();
        }
        $food = Foods::where('slug', $foodSlug)->where('expiry_date', '>', $currentDateTime)->first();
        if (empty($food) || $food->status == 2 || $food->status == 4) {
            return View('error404');
        }
        $imageUrls = ImagesFood::where('food_id', $food->id)->pluck('image_url')->toArray();
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
        $collected = false;
        $fourHoursAgo = Carbon::now()->subHours(4);
        $collectedSuccess = false;
        if ($food->foodTransactions) {
            foreach ($food->foodTransactions as $transaction) {
                $transactionRatings = Rate::where('food_transaction_id', $transaction->id)->first();
                if (isset($transactionRatings)) {
                    $userRating = Users::find($transaction->receiver_id);
                    $ratings[$transaction->id] = ['rating' => $transactionRatings, 'user' => $userRating];
                } else {
                    $ratings[$transaction->id] = null;
                }
                if ($userCurrent) {
                    if ($transaction->receiver_id == $userCurrent->id && Carbon::parse($transaction->created_at)->gt($fourHoursAgo)) {
                        $collected = true;
                    }
                }
            }
        }
        if ($userCurrent) {
            $userCurrentTransactions = FoodTransactions::where('receiver_id', $userCurrent->id)->get();
            foreach ($userCurrentTransactions as $transaction) {
                if (Carbon::parse($transaction->created_at)->gt($fourHoursAgo) && $transaction->status == 1) {
                    $collectedSuccess = true;
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
        return response()->json(['food' => $combinedData, 'ratings' => $ratings, 'isSubscribed' => $isSubscribed, 'collected' => $collected, 'collectedSuccess' => $collectedSuccess]);
    }

    public function getFoodWithCategory($category, Request $request)
    {
        $perPage = $request['_limit'] ?? 8;
        $sort = $request['_sort'] ?? null;
        $page = $request['_page'] ?? 1;
        $min_rating = $request['min_rating'] ?? null;
        $food_type = $request['food_type'] ?? null;
        $collect_type = $request['collect_type'] ?? null;
        $district = $request['district_name'] ?? null;
        $province = $request['province_name'] ?? null;
        $commune = $request['ward_name'] ?? null;
        $currentDateTime = Carbon::now();
        $query = Foods::join('addresses', 'food.address_id', '=', 'addresses.id')
            ->join('categories', 'food.category_id', '=', 'categories.id')
            ->select(
                'food.*',
                'food.id',
                'addresses.id as address_id',
                'addresses.province',
                'addresses.district',
                'addresses.commune',
                'addresses.lon',
                'addresses.lat',
                'addresses.formatted_address',
                'addresses.contact_information',
                'addresses.home_number',
                'categories.slug as category_slug',
                DB::raw('(SELECT AVG(rating) FROM rates WHERE food_transaction_id IN 
                (SELECT id FROM food_transactions WHERE food_id = food.id)) as average_rating'),
            )
            ->where('food.quantity', '>', 0)
            ->where('food.expiry_date', '>', $currentDateTime)
            ->whereNotIn('food.status', [2, 4])
            ->with('images');

        if ($category != 'tat-ca-thuc-pham') {
            $query->where('categories.slug', $category);
        }
        if ($request->has('searchContent')) {
            $searchContent = $request->input('searchContent');
            session(['searchContent' => $searchContent]);
            $query->where(function ($q) use ($searchContent) {
                $q->where('food.title', 'like', '%' . $searchContent . '%')
                    ->orWhere('food.description', 'like', '%' . $searchContent . '%');
            });
        }

        if ($province != null) {
            $query->where('addresses.province', $province);
        }

        if ($district != null) {
            $query->where('addresses.district', $district);
        }

        if ($commune != null) {
            $query->where('addresses.commune', $commune);
        }

        if ($sort != null) {
            if ($sort === 'time_asc') {
                $query->orderBy('food.created_at', 'asc');
            } elseif ($sort === 'time_desc') {
                $query->orderBy('food.created_at', 'desc');
            } elseif ($sort === 'rating_desc') {
                $query->orderBy('average_rating', 'desc');
            } elseif ($sort === 'rating_asc') {
                $query->orderBy('average_rating', 'asc');
            }
        } else {
            $query->orderBy('average_rating', 'desc');
        }

        if ($food_type != null) {
            $query->where('food.food_type', $food_type);
        }

        if ($collect_type != null) {
            $query->where('food.collect_type', $collect_type);
        }
        if ($min_rating != null) {
            $query->where(DB::raw('(SELECT AVG(rating) FROM rates WHERE food_transaction_id IN 
                (SELECT id FROM food_transactions WHERE food_id = food.id))'), '>=', $min_rating);
        }
        $totalItems = $query->count();
        $totalPages = ceil($totalItems / $perPage);
        $products = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json(['food' => $products, 'totalPage' => $totalPages], 200);
    }
    public function getProvinces(Request $request)
    {
        $categories = province::all();
        return response()->json($categories);
    }
    public function getAllDistrictOfProvinceId($provinceID)
    {
        $districts = District::where('province_id', $provinceID)->get();
        if (!$districts) {
            return response()->json(['error' => 'Không Tồn Tại Tỉnh Này'], 404);
        }
        return response()->json($districts);
    }
    public function getAllWardOfDistrictId($districtID)
    {
        $wards = ward::where('district_id', $districtID)->get();
        if (!$wards) {
            return response()->json(['error' => 'Không Tồn Tại Huyện Này'], 404);
        }
        return response()->json($wards);
    }
    public function getNameProvince($provinceId)
    {
        $province = province::where('id', $provinceId)->first();
        if (!$province) {
            return response()->json(['error' => 'Không Tồn Tại Tỉnh Này'], 404);
        }
        return response()->json($province);
    }
    public function getNameDistrict($districtId)
    {
        $district = district::where('id', $districtId)->first();
        if (!$district) {
            return response()->json(['error' => 'Không Tồn Tại Huyện Này'], 404);
        }
        return response()->json($district);
    }
    public function getNameWard($wardId)
    {
        $ward = ward::where('id', $wardId)->first();
        if (!$ward) {
            return response()->json(['error' => 'Không Tồn Tại Xã Này'], 404);
        }
        return response()->json($ward);
    }

    public function addDonateFood(DonateFoodRequest $request)
    {
        $user = auth()->user();
        if ($user) {
            $userId = $user->id;
        } else {
            $userId = null;
        }
        $food = new Foods();
        $expiryDate = Carbon::parse($request->input('expiry_date'));
        $food->user_id = $userId;
        $food->category_id = $request->input('category_id');
        $food->title = $request->input('title');
        $food->food_type = $request->input('food_type');
        $food->description = $request->input('description');
        $food->quantity = $request->input('quantity');
        $food->food_type = $request->input('food_type');
        $food->expiry_date = $expiryDate;
        $food->remaining_time_to_accept = $request->input('confirm_time');
        $food->address_id = $request->input('address_id');
        $slug_food = Str::slug($request->input('title')) . '_' . time();
        $food->slug = $slug_food;
        $food->save();
        if ($request->hasFile('images_food')) {
            $imageUrls = [];
            foreach ($request->file('images_food') as $image) {
                $randomTitle = random_int(100000, 999999);
                $file_name = Str::slug($request->input('title')) . '_' . Str::slug($randomTitle) . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/food_images'), $file_name);
                $imageUrls[] = '../../uploads/food_images/' . $file_name;
            }
            foreach ($imageUrls as $imageUrl) {
                ImagesFood::create([
                    'food_id' => $food->id,
                    'image_url' => $imageUrl,
                ]);
            }
        } else {
            return response()->json(['errors' => 'Vui lòng chọn ít nhất 1 ảnh'], 422);
        }
        $subscribers = NotificationSubscribers::where('sender_id', $food->user_id)
            ->where('notification_type', 1)
            ->get();
        foreach ($subscribers as $subscriber) {
            $notification = new DetailNotificationSubscribers();
            $notification->food_id = $food->id;
            $notification->sub_id = $subscriber->id;
            $notification->notification_subscriber_id = $subscriber->receiver_id;
            $notification->type = 0;
            $notification->is_read = 0;
            $notification->user_image = $user->image;
            $notification->message = $user->full_name . ' đã bắt đầu tặng ' . $food->title . ' với số lượng là: ' . $food->quantity . '. Bạn hãy xem để tránh bỏ lỡ thực phẩm bạn nhé!';
            $notification->save();
        }
        return response()->json(['message' => 'Tặng thực phẩm thành công'], 200);

    }
    public function getDonateList(request $request)
    {
        $user = auth()->user();
        $perPage = $request->input('_limit', 10);
        $page = $request->input('_page', 1);
        if ($user) {
            $donatedFoods = Foods::where('user_id', $user->id)->with('images')->paginate($perPage, ['*'], 'page', $page);
            ;
            return response()->json(['donatedFoods' => $donatedFoods]);
        }
        return response()->json(['error' => 'Không tìm thấy tài khoản']);
    }
    public function cancelDonateFood(Request $request)
    {
        $food_id = $request['food_id'];
        $food = Foods::find($food_id);
        if (!$food) {
            return response()->json(['errors' => 'Không tìm thấy thực phẩm.'], 404);
        }
        $food->status = 2;
        $food->save();
        if ($food) {
            $message = $request['message'];
            $user = auth()->user();
            $transactions = $food->foodTransactions;
            foreach ($transactions as $transaction) {
                $receiver = $transaction->receiver;
                if ($receiver->id !== $food->user_id) {
                    $notification = new Notification([
                        'user_id' => $receiver->id,
                        'transaction_id' => $transaction->id,
                        'message' => 'Người tặng thực phẩm gửi thông báo: ' . $message,
                        'image' => $user->image,
                        'is_read' => 0,
                        'type' => '404',
                    ]);
                    $notification->save();
                }
            }
        }
        return response()->json(['message' => 'Đã dừng tặng thực phẩm thành công.']);
    }
    public function foodDonatedDetail($foodId)
    {
        $food = Foods::where('id', $foodId)->first();
        if (empty($food)) { 
            return View('error404');
        }
        $imageUrls = ImagesFood::where('food_id', $food->id)->pluck('image_url')->toArray();
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
        $userratings = [];
        if ($food->foodTransactions) {
            foreach ($food->foodTransactions as $transaction) {
                $transactions[] = $transaction;
                $transactionRatings = Rate::where('food_transaction_id', $transaction->id)->first();

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
        return response()->json(['food' => $combinedData, 'ratings' => $ratings, 'isSubscribed' => $isSubscribed]);
    }
    public function editDonateFood(EditFoodDonatedRequest $request)
    {
        $user = auth()->user();
        if ($user) {
            $userId = $user->id;
        } else {
            $userId = null;
        }
        $food = Foods::find($request->input('id'));
        $expiryDate = Carbon::parse($request->input('expiry_date'));
        $food->user_id = $userId;
        $food->category_id = $request->input('category_id');
        $food->title = $request->input('title');
        $food->food_type = $request->input('food_type');
        $food->description = $request->input('description');
        $food->quantity = $request->input('quantity');
        $food->food_type = $request->input('food_type');
        $food->expiry_date = $expiryDate;
        $food->remaining_time_to_accept = $request->input('confirm_time');
        $food->address_id = $request->input('address_id');
        $slug_food = Str::slug($request->input('title')) . '_' . time();
        $food->slug = $slug_food;
        $food->save();
        if ($request->hasFile('images_food') && $request->file('images_food')[0] !== null) {
            ImagesFood::where('food_id', $request->input('id'))->delete();
            $imageUrls = [];
            foreach ($request->file('images_food') as $image) {
                $randomTitle = random_int(100000, 999999);
                $file_name = Str::slug($request->input('title')) . '_' . Str::slug($randomTitle) . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/food_images'), $file_name);
                $imageUrls[] = '../../uploads/food_images/' . $file_name;
            }
            foreach ($imageUrls as $imageUrl) {
                ImagesFood::create([
                    'food_id' => $food->id,
                    'image_url' => $imageUrl,
                ]);
            }
        }
        return response()->json(['message' => 'Sửa thực phẩm thành công'], 200);
    }
    public function getDetailPageReceiverList($foodId)
    {
        $food = Foods::where('id', $foodId)->first();
        if ($food) {
            $transactions = $food->foodTransactions;
            $receiverList = [];

            foreach ($transactions as $transaction) {
                $receiver = $transaction->receiver;

                $receiverInfo = [
                    'transaction_created_at' => $transaction->created_at,
                    'status' => $transaction->status,
                    'receiver_status' => $transaction->receiver_status,
                    'donor_status' => $transaction->donor_status,
                    'anonymous' => $transaction->anonymous,
                ];
                if ($transaction->anonymous != 1) {
                    $receiverInfo += [
                        'receiver_full_name' => $receiver->full_name,
                        'image' => $receiver->image,
                        'status' => $transaction->status,
                        'receiver_status' => $transaction->receiver_status,
                        'donor_status' => $transaction->donor_status,
                        'anonymous' => $transaction->anonymous,
                    ];
                }
                $receiverList[] = $receiverInfo;
            }

            return response()->json(['receiverList' => $receiverList], 200);
        }

        return response()->json(['error' => 'Food not found'], 404);
    }
}
