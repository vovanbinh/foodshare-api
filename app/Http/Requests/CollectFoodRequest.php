<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CollectFoodRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'value.Quantity' => 'required|integer|min:1',
        ];
    }
    public function messages()
    {
        return [
            'value.Quantity.required' => 'Vui lòng nhập số lượng.',
            'value.Quantity.integer' => 'Số lượng phải là số nguyên.',
            'value.Quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
            'value.anonymous.in' => 'Ẩn danh không hợp lệ',
        ];
    }
}
