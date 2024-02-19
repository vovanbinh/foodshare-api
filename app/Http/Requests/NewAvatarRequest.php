<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewAvatarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'image' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'image.image' => 'Vui lòng chọn hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg hoặc gif.',
            'image.max' => 'Ảnh không được vượt quá 2MB.',
        ];
    }


}
