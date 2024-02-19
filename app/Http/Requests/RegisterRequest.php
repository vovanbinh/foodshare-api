<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'full_name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|min:8|max:30',
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => 'Vui lòng nhập đầy đủ tên.',
            'full_name.max' => 'Tên không được vượt quá 50 ký tự.',
            'email.required' => 'Địa chỉ email là bắt buộc.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.unique' => 'Địa chỉ email đã tồn tại.',
            'email.max' => 'Địa chỉ email không được vượt quá 100 ký tự.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải chứa ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu không được vượt quá 30 ký tự.',
        ];
    }
}
