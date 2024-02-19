<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DonateFoodRequest extends FormRequest
{
    public function authorize()
    { 
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string|max:1000',
            'quantity' => 'required|integer|min:1',
            'expiry_date' => 'required|date|after_or_equal:today',
            'confirm_time' => 'required|in:30,60,90,120,150,180',
            'food_type' => 'required|in:da-che-bien,chua-che-bien',
            'address_id' => 'required|exists:addresses,id',
            'images_food' => 'required|array',
            'images_food.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'category_id.required' => 'Vui lòng chọn Danh mục.',
            'category_id.in' => 'Danh mục không hợp lệ.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'description.max' => 'Vui lòng nhập mô tả ngắn hơn.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
            'expiry_date.required' => 'Vui lòng nhập thời gian hết hạn.',
            'expiry_date.date' => 'Thời gian hết hạn không hợp lệ.',
            'after_or_equal.date' => 'Thời gian hết hạn phải sau thời gian hiện tại.',
            'confirm_time.required' => 'Vui lòng nhập thời gian chấp nhận.',
            'confirm_time.in' => 'Thời gian chấp nhận không hợp lệ.',
            'food_type.required' => 'Vui lòng chọn trạng thái thực phẩm.',
            'food_type.in' => 'Trạng thái thực phẩm không hợp lệ.',
            'address_id.required' => 'Vui lòng thêm địa điểm hợp lệ.',
            'address_id.in' => 'Tỉnh/Thành Phố không hợp lệ.',
            'images_food.required' => 'Vui lòng thêm ít nhất 1 ảnh.',
            'images_food.*.image' => 'Tất cả các ảnh mô tả phải là hình ảnh.',
            'images_food.*.mimes' => 'Tất cả các ảnh mô tả phải có định dạng jpeg, png, jpg hoặc gif.',
            'images_food.*.max' => 'Tất cả các ảnh mô tả không được vượt quá 2MB.',
        ];
    }
}
