<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddNewAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddreaasController extends Controller
{
    // public function addNewAddress(AddNewAddressRequest $request)
    // {
    //     $user = auth()->user();
    //     $existingAddress = Address::where('user_id', $user->id)
    //         ->where('province_id', $request->input('province_id'))
    //         ->where('district_id', $request->input('district_id'))
    //         ->where('ward_id', $request->input('ward_id'))
    //         ->where('location', $request->input('ward_id'))
    //         ->where('contact_information', $request->input('ward_id'))
    //         // ->where('note', $request->input('note'))
    //         ->first();
    //     if ($existingAddress) {
    //         return response()->json(['errors' => ['Địa này chỉ đã tồn tại']], 422);
    //     }
    //     $address = new Address();
    //     $address->user_id = $user->id;
    //     $address->province_id = $request->input('province_id');
    //     $address->district_id = $request->input('district_id');
    //     $address->ward_id = $request->input('ward_id');
    //     $address->contact_information = $request->input('contact_information');
    //     $address->location = $request->input('location');
    //     $address->note = Address::where('user_id', $user->id)->where('note', 1)->count() == 0 ? 1 : 0;
    //     $address->save();
    //     return response()->json(['message' => 'Thêm mới địa chỉ thành công'], 200);
    // }
    public function addNewAddress(AddNewAddressRequest $request)
    {
        $data = $request['data'];
        $detailAddress = $request['detailAddress'];
        $formatted_address = $detailAddress['formatted_address'];
        $lat = $detailAddress['geometry']['location']['lat'];
        $lon = $detailAddress['geometry']['location']['lng'];
        $district = $detailAddress['compound']['district'];
        $commune = $detailAddress['compound']['commune'];
        $province = $detailAddress['compound']['province'];
        $name = $detailAddress['name'];
        $user = auth()->user();
        $existingAddress = Address::where('user_id', $user->id)
            ->where('lat', $lat)
            ->where('lon', $lon)
            ->where('note', 0)
            ->first();
        if ($existingAddress) {
            return response()->json(['errors' => ['Địa này chỉ đã tồn tại']], 422);
        }
        $address = new Address();
        $address->user_id = $user->id;
        $address->lon = $lon;
        $address->lat = $lat;
        $address->home_number = $name;
        $address->contact_information = $data['contact_information'];
        $address->district = $district;
        $address->province = $province;
        $address->commune = $commune;
        $address->formatted_address = $formatted_address;
        $address->note = Address::where('user_id', $user->id)->where('note', true)->count() == false ? true : false;
        $address->save();
        return response()->json(['message' => 'Thêm mới địa chỉ thành công'], 200);
    }
    public function getAllAddress(Request $request)
    {
        $user = auth()->user();
        $addresses = Address::with('province', 'district', 'ward')
            ->where('user_id', $user->id)
            ->where('note', '<>', 2)
            ->get();
        return response()->json($addresses);
    }

    public function updateAddress(UpdateAddressRequest $request)
    {
        $data = $request['data'];
        $detailAddress = $request['detailAddressNew'] ?? null;
        $user = auth()->user();
        if ($detailAddress) {
            $formatted_address = $detailAddress['formatted_address'];
            $lat = $detailAddress['geometry']['location']['lat'];
            $lon = $detailAddress['geometry']['location']['lng'];
            $district = $detailAddress['compound']['district'];
            $commune = $detailAddress['compound']['commune'];
            $province = $detailAddress['compound']['province'];
            $name = $detailAddress['name'];

            $existingAddress = Address::where('user_id', $user->id)
                ->where('lat', $lat)
                ->where('lon', $lon)
                ->where('note', $data['note'])
                ->first();
            if ($existingAddress) {
                return response()->json(['errors' => ['Địa chỉ đã tồn tại']], 422);
            }
        }
        $address = Address::find($data['id']);
        if (!$address) {
            return response()->json(['errors' => ['Địa chỉ không tồn tại']], 422);
        }
        if ($detailAddress) {
            $address->lon = $lon;
            $address->lat = $lat;
            $address->home_number = $name;
            $address->district = $district;
            $address->province = $province;
            $address->commune = $commune;
            $address->formatted_address = $formatted_address;
        }
        $address->contact_information = $data['contact_information'];
        if ($data['note']) {
            $address->note = true;
        }
        $address->save();
        if ($data['note'] === true) {
            Address::where('user_id', $user->id)
                ->where('note', true)
                ->where('id', '!=', $data['id'])
                ->update(['note' => false]);
        }
        return response()->json(['message' => 'Cập nhật địa chỉ thành công'], 200);
    }
    public function deleteAddress(Request $request)
    {
        $addressId = $request[0];
        $user = auth()->user();
        $address = Address::where('user_id', $user->id)
            ->where('id', $addressId)
            ->first();

        if (!$address) {
            return response()->json(['errors' => 'Không tìm thấy địa chỉ.'], 404);
        }

        $previousNote = $address->note;
        if ($previousNote == true) {
            $newNote1Address = Address::where('user_id', $user->id)
                ->where('id', '!=', $addressId)
                ->where('note', false)
                ->first();
            if ($newNote1Address) {
                $newNote1Address->note = true;
                $newNote1Address->save();
            }
        }
        $address->note = 2;
        $address->save();
        return response()->json(['message' => 'Xóa địa chỉ thành công']);
    }
}
