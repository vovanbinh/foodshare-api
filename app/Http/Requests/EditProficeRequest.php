<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditProficeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'full_name' => 'required|string|max:50|min:4',
            'phone_number' => 'required|regex:/^\d{10}$/u',
            'birthdate' => 'required|date',
        ];
    }
    public function messages()
    {
        return [
            'full_name.string' => 'Họ và Tên phải là một chuỗi.',
            'full_name.max' => 'Họ và Tên không được vượt quá 50 ký tự.',
            'full_name.min' => 'Họ và Tên phải có ít nhất 4 ký tự.',
            'full_name.required' => 'Vui lòng nhập Họ và tên.',
            'phone_number.regex' => 'Số điện thoại không hợp lệ. Hãy nhập 10 chữ số.',
            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'birthdate.date' => 'Ngày không hợp lệ.',
            'birthdate.required' => 'Vui lòng nhập ngày sinh.',
        ];
    }
}
