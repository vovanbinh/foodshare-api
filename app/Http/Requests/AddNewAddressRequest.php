<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddNewAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'data.contact_information' => 'required|string|max:255',
            'detailAddress.formatted_address' => 'required|string|max:255',
            'detailAddress.geometry.location.lat' => 'required|numeric',
            'detailAddress.geometry.location.lng' => 'required|numeric',
            'detailAddress.compound.district' => 'required|string|max:255',
            'detailAddress.compound.commune' => 'required|string|max:255',
            'detailAddress.compound.province' => 'required|string|max:255',
            'detailAddress.name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'contact_information.required' => 'Vui lòng nhập thông tin liên hệ.',
            'data.contact_information.max' => 'Thông tin liên hệ không được vượt quá 255 ký tự.',
            'detailAddress.formatted_address.required' => 'Vui lòng nhập địa chỉ đầy đủ.',
            'detailAddress.formatted_address.max' => 'Địa chỉ đầy đủ không được vượt quá 255 ký tự.',
            'detailAddress.geometry.location.lat.required' => 'Vui lòng nhập giá trị lat.',
            'detailAddress.geometry.location.lat.numeric' => 'Giá trị lat phải là số.',
            'detailAddress.geometry.location.lng.required' => 'Vui lòng nhập giá trị lng.',
            'detailAddress.geometry.location.lng.numeric' => 'Giá trị lng phải là số.',
            'detailAddress.compound.district.required' => 'Vui lòng nhập quận/huyện.',
            'detailAddress.compound.district.max' => 'Quận/huyện không được vượt quá 255 ký tự.',
            'detailAddress.compound.commune.required' => 'Vui lòng nhập xã/phường.',
            'detailAddress.compound.commune.max' => 'Xã/phường không được vượt quá 255 ký tự.',
            'detailAddress.compound.province.required' => 'Vui lòng nhập tỉnh/thành phố.',
            'detailAddress.compound.province.max' => 'Tỉnh/thành phố không được vượt quá 255 ký tự.',
            'detailAddress.name.required' => 'Vui lòng nhập tên địa điểm.',
            'detailAddress.name.max' => 'Tên địa điểm không được vượt quá 255 ký tự.',
        ];
    }
}
