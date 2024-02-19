<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'data.id' => 'required|exists:addresses,id',
            'data.contact_information' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'data.id.in' => 'Địa chỉ không hợp lệ.',
            'data.contact_information.required' => 'Vui lòng nhập thông tin liên hệ.',
            'data.contact_information.max' => 'Thông tin liên hệ không được vượt quá 255 ký tự.',

        ];
    }
}
