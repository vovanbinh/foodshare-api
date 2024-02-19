<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Jobs\SendEmailJob;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth::api", ['except' => ['login', 'loginGoogle', 'loginFacebook', 'register', 'verification', 'forgotPassword', 'verificationForgot', 'NewPasswordForgot']]);
    }
    public function login(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $user = Users::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            if ($user->is_verified == 0) {
                $verificationCode = random_int(100000, 999999);
                $user->verification_code = $verificationCode;
                if ($user->save()) {
                    dispatch(new SendEmailJob($email, $user->full_name, $verificationCode));
                    return response()->json(['unVerification' => ['Vui lòng xác thực tài khoản để tiếp tục']], 200);
                }
            }
            $credentials = $request->only('email', 'password');
            $token = Auth::attempt($credentials);
            if (!$token) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
            $user = Auth::user();
            return response()->json([
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        } else {
            return response()->json(['errors' => ['Tên đăng nhập hoặc mật khẩu không đúng']], 422);
        }
    }
    private function getGoogleUserData($token)
    {
        $url = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $token;
        $response = file_get_contents($url);
        $userData = json_decode($response, true);
        return $userData;
    }
    public function loginGoogle(Request $request)
    {
        try {
            $token = $request->input('credential');
            $userData = $this->getGoogleUserData($token);

            $email = $userData['email'];
            $sub = $userData['sub'];
            $name = $userData['name'];
            $user = Users::where('email', $email)->first();
            if (!$user) {
                $user = Users::create([
                    'email' => $email,
                    'password' => Hash::make($sub),
                    'role' => 0,
                    'full_name' => $name,
                    'type' => 'google',
                ]);
            }
            if (!$token = Auth::attempt(['email' => $email, 'password' => $sub])) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
            $user = Auth::user();
            return response()->json([
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function loginFacebook(Request $request)
    {
        try {
            if ($request['email'] != null) {
                $email = $request['email'];
            } else {
                $email = $request['userID'];
            }
            $name = $request['name'];
            $user = Users::where('email', $email)->first();
            if (!$user) {
                $user = Users::create([
                    'email' => $email,
                    'password' => Hash::make($request['userID']),
                    'role' => 0,
                    'full_name' => $name,
                    'type' => 'facebook',
                ]);
            }
            if (!$token = Auth::attempt(['email' => $email, 'password' => $request['userID']])) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
            $user = Auth::user();
            return response()->json([
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function register(RegisterRequest $request)
    {
        $verificationCode = random_int(100000, 999999);
        $user = Users::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'role' => 0,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
        ]);
        dispatch(new SendEmailJob($request->email, $request->full_name, $verificationCode));
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }
    public function verification(request $request)
    {
        $validator = Validator::make($request->all(), [
            'verification_code' => 'required',
        ], [
            'verification_code.required' => 'Vui lòng nhập mã xác thực.',
        ]);
        if ($validator->passes()) {
            $verification_code = $request->verification_code;
            $email = $request->email;
            $user = Users::where('email', $email)->first();
            if (!$user) {
                return response()->json(['errors' => ['Tài khoản không tồn tại']], 400);
            } else {
                if ($user->verification_code === $verification_code) {
                    $user->is_verified = 1;
                    if ($user->save()) {
                        return response()->json(['message' => 'Xác thực thành công, vui lòng đăng nhập'], 200);
                    } else {
                        return response()->json(['errors' => ['Failed to update user']], 500);
                    }
                } else {
                    return response()->json(['errors' => 'Mã xác thực không đúng'], 200);
                }
            }
        }
        $errors = $validator->errors()->all();
        return response()->json(['errors' => $errors], 200);
    }
    public function verificationForgot(request $request)
    {
        $validator = Validator::make($request->all(), [
            'verification_code' => 'required',
        ], [
            'verification_code.required' => 'Vui lòng nhập mã xác thực.',
        ]);
        if ($validator->passes()) {
            $verification_code = $request->verification_code;
            $email = $request->email;
            $user = Users::where('email', $email)->first();
            if (!$user) {
                return response()->json(['errors' => ['Tài khoản không tồn tại']], 400);
            } else {
                if ($user->forgot_password_code === $verification_code) {
                    $user->is_verified = 1;
                    if ($user->save()) {
                        return response()->json(['message' => 'Xác thực thành công, vui lòng thay đổi mật khẩu'], 200);
                    } else {
                        return response()->json(['errors' => ['Failed to update user']], 500);
                    }
                } else {
                    return response()->json(['errors' => 'Mã xác thực không đúng'], 200);
                }
            }
        }
        $errors = $validator->errors()->all();
        return response()->json(['errors' => $errors], 200);
    }
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
        ]);

        if ($validator->passes()) {
            $email = $request->input('email');
            $user = Users::where('email', $email)->first();
            if ($user) {
                $forgot_password_code = random_int(100000, 999999);
                $user->forgot_password_code = $forgot_password_code;
                $user->save();
                dispatch(new SendEmailJob($email, $user->full_name, $forgot_password_code));
                return response()->json(['message' => 'Vui lòng xác thực'], 200);
            } else {
                return response()->json(['errors' => 'Email không tồn tại'], 200);
            }
        }
        $errors = $validator->errors()->all();
        return response()->json(['errors' => $errors], 200);
    }
    public function NewPasswordForgot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|max:30',
        ], [
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu không được vượt quá 30 ký tự.',
        ]);

        if ($validator->passes()) {
            $email = $request->email;
            $password = $request->password;
            $user = Users::where('email', $email)->first();
            if ($user) {
                $hashedPassword = Hash::make($password);
                $user->password = $hashedPassword;
                $user->save();
                return response()->json(['message' => 'Thay đổi mật khẩu thành công, vui lòng đăng nhập'], 200);
            } else {
                return response()->json(['errors' => 'Email không tồn tại'], 200);
            }
        }
        $errors = $validator->errors()->all();
        return response()->json(['errors' => $errors], 200);
    }
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function getAllUsers()
    {
        $users = Users::all();

        return response()->json($users);
    }
}
