<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewPasswordRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'old_password' => 'required',
            'password' => 'required|min:8|max:30',
            're_password' => 'required|same:password',
        ];
    }
    public function messages()
    {
        return [
            'old_password.required' => 'Mật khẩu cũ là bắt buộc.',
            'password.required' => 'Mật khẩu mới là bắt buộc.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu không được vượt quá 30 ký tự.',
            're_password.required' => 'Xác nhận mật khẩu là bắt buộc.',
            're_password.same' => 'Xác nhận mật khẩu không khớp với mật khẩu.',
        ];
    }
}
