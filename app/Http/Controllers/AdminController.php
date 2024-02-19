<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\categories;
use App\Models\Foods;
use App\Models\FoodTransactions;
use App\Models\ImagesFood;
use App\Models\Notification;
use App\Models\Rate;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Email là bắt buộc.',
            'password.required' => 'Mật khẩu là bắt buộc.',
        ]);
        if ($validator->passes()) {
            $email = $request->input('email');
            $password = $request->input('password');
            $user = Users::where('email', $email)->first();
            if ($user && Hash::check($password, $user->password)) {
                Session::put('id', $user->id);
                if ($user->role == 1) {
                    return response()->json(['admin' => 'Đăng nhập thành công'], 200);
                }
                return response()->json(['message' => 'Đăng nhập thành công'], 200);
            } else {
                return response()->json(['errors' => ['Tên đăng nhập hoặc mật khẩu không đúng']], 200);
            }
        }
        $errors = $validator->errors()->all();
        return response()->json(['errors' => $errors], 200);
    }

    public function view_Charts()
    {
        return view('admin/charts');
    }

    public function getChartData($timeRange)
    {
        $chartData = [];
        if ($timeRange === 'month') {
            $chartData = $this->getDataForMonth();
        } elseif ($timeRange === 'week') {
            $chartData = $this->getDataForWeek();
        } else {
            $chartData = $this->getDataForYear();
        }
        return response()->json(['chartData' => $chartData]);
    }

    private function getDataForMonth()
    {
        $data = [];
        for ($i = 1; $i <= 31; $i++) {
            $date = now()->startOfMonth()->addDays($i - 1)->toDateString();
            $foodsCount = Foods::whereDate('created_at', $date)->count();
            $transactionsCount = FoodTransactions::whereDate('created_at', $date)->count();
            $usersCount = Users::whereDate('created_at', $date)->count();
            $data[] = [
                'date' => $date,
                'foods' => $foodsCount,
                'transactions' => $transactionsCount,
                'users' => $usersCount,
            ];
        }
        return $data;
    }
    private function getDataForWeek()
    {
        $data = [];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->startOfWeek()->addDays($i)->toDateString();
            $foodsCount = Foods::whereDate('created_at', $date)->count();
            $transactionsCount = FoodTransactions::whereDate('created_at', $date)->count();
            $usersCount = Users::whereDate('created_at', $date)->count();
            $data[] = [
                'date' => $date,
                'foods' => $foodsCount,
                'transactions' => $transactionsCount,
                'users' => $usersCount,
            ];
        }
        return $data;
    }
    private function getDataForYear()
    {
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $date = now()->startOfYear()->addMonths($i - 1)->toDateString();
            $foodsCount = Foods::whereYear('created_at', now()->year)->whereMonth('created_at', $i)->count();
            $transactionsCount = FoodTransactions::whereYear('created_at', now()->year)->whereMonth('created_at', $i)->count();
            $usersCount = Users::whereYear('created_at', now()->year)->whereMonth('created_at', $i)->count();
            $data[] = [
                'date' => $date,
                'foods' => $foodsCount,
                'transactions' => $transactionsCount,
                'users' => $usersCount,
            ];
        }

        return $data;
    }
    public function show_dashboard()
    {
        $today = now()->format('Y-m-d');
        $transactionCounttoday = DB::table('food_transactions')
            ->whereDate('created_at', $today)
            ->count();
        $transactionCount = DB::table('food_transactions')->count();
        $userCount = DB::table('users')->count();
        $foodCount = DB::table('food')->count();
        return view("admin.admin_dashboard", [
            'transactionCounttoday' => $transactionCount,
            'transactionCount' => $transactionCount,
            'userCount' => $userCount,
            'foodCount' => $foodCount,
        ]);
    }
    public function logout()
    {
        session()->forget('id');
        return redirect()->route('login');
    }
    public function show_manage_donated(Request $request)
    {
        $query = Foods::with(['user', 'images'])
            ->orderBy('created_at', 'desc');
        if ($request->has('searchContent')) {
            $query->where('title', 'like', '%' . $request->input('searchContent') . '%');
        }
        if ($request->has('category_id') && $request->input('category_id') !== 'null') {
            session(['category_id' => $request->input('category_id')]);
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->has('food_status') && $request->input('food_status') !== 'null') {
            $query->where('status', $request->input('food_status'));
        }
        $foods = $query->paginate(10);
        $categories = categories::all();

        return view('admin/manage_donated', compact('foods', 'categories'));
    }
    public function show_manage_transactions(Request $request)
    {
        $request->session()->forget('searchContent');
        $transaction_status = $request->input('transaction_status');
        $transactions = FoodTransactions::with('receiver', 'food', 'food.images')
            ->orderBy('created_at', 'desc');
        if ($request->has('searchContent')) {
            $searchContent = $request->input('searchContent');
            session(['searchContent' => $searchContent]);
            $transactions = $transactions->where(function ($query) use ($searchContent) {
                $query->orWhereHas('food', function ($foodQuery) use ($searchContent) {
                    $foodQuery->where('title', 'like', '%' . $searchContent . '%');
                })
                    ->orWhereHas('receiver', function ($userQuery) use ($searchContent) {
                        $userQuery->where('full_name', 'like', '%' . $searchContent . '%');
                    });
            });
        }
        if ($request->has('transaction_status') && $transaction_status != 'null') {
            $transactions = $transactions->where('status', $transaction_status);
        }
        $transactions = $transactions->paginate(10);
        return view('admin/manage_transactions', compact('transactions'));
    }
    public function show_manage_users(Request $request)
    {
        $request->session()->forget('searchContent');
        $user_role = $request->input('user_role');
        $user_status = $request->input('user_status');

        $users = Users::orderBy('created_at', 'desc');

        if ($request->has('searchContent')) {
            $searchContent_user = $request->input('searchContent');
            session(['searchContent' => $searchContent_user]);
            $users = $users->where(function ($query) use ($searchContent_user) {
                $query->where('full_name', 'like', '%' . $searchContent_user . '%')
                    ->orWhere('full_name', 'like', '%' . $searchContent_user . '%');
            });
        }

        if ($request->has('user_role') && $user_role != 'null') {
            $users = $users->where('role', $user_role);
        }

        if ($request->has('user_status') && $user_status != 'null') {
            $users = $users->where('is_verified', $user_status);
        }

        $users = $users->paginate(10);
        return view('admin/manage_users', compact('users'));
    }

    public function lock_donated($lock_id)
    {
        $food = Foods::find($lock_id);
        if (!$food) {
            return response()->json(['errors' => 'Không tìm thấy thực phẩm này'], 404);
        }
        $food->status = 4;
        $food->save();
        $foodTransactions = FoodTransactions::where('food_id', $food->id)->get();
        foreach ($foodTransactions as $foodTransaction) {
            $foodTransaction->status = 4;
            $foodTransaction->save();
        }
        return response()->json(['message' => 'Cập nhật thành công']);
    }

    public function manage_donated_detail($food_donated_id)
    {
        $food = Foods::find($food_donated_id);

        if (empty($food)) {
            return view('error404');
        }
        $imageUrls = ImagesFood::where('food_id', $food_donated_id)->pluck('image_url')->toArray();
        $user = Users::find($food->user_id);
        $address = Address::find($food->address_id);
        $contact_information = $address->contact_information;
        $formatted_address = $address->formatted_address;
        $foodData = $food->toArray();
        $combinedData = array_merge(
            $foodData,
            [
                'imageUrls' => $imageUrls,
                'user' => $user,
                'formatted_address' => $address->formatted_address,
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
                    $ratings[$transaction->id] = $transactionRatings;
                } else {
                    $ratings[$transaction->id] = null;
                }
            }
        }
        $categories = categories::all();
        return view('admin/manage_donated_detail', compact('foodData','formatted_address' ,'imageUrls', 'user', 'ratings', 'userratings', 'categories'));

    }
    public function lock_user($lock_id)
    {
        $user = Users::find($lock_id);
        if (!$user) {
            return response()->json(['errors' => 'Không tìm thấy tài khoản này'], 404);
        }
        $user->is_verified = 3;
        $user->save();
        return response()->json(['message' => 'Khóa Tài Khoản Thành Công']);
    }
    public function show_role_user($user_id)
    {
        $user = Users::find($user_id);
        if (!$user) {
            return View('error404');
        }
        return view('admin/manage_user_role', compact('user'));
    }
    public function role_user($user_id, $role)
    {
        $user = Users::find($user_id);
        if (!$user) {
            return View('error404');
        } else if ($user->is_verified == 3) {
            return View('error404');
        } else if ($role != 0 && $role != 1) {
            return View('error404');
        } else {
            $user->role = $role;
            $user->save();
            return response()->json(['message' => 'Cập Nhật Quyền Thành Công']);
        }
    }
    public function show_error_notification()
    {
        $notifications = Notification::with(['user', 'food'])->where('user_id', 120)->orderBy('created_at', 'desc')->get();
        return View('admin/manage_error_notice', compact('notifications'));
    }
}