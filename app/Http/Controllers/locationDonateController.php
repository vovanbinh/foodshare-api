<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Food_Locations;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class locationDonateController extends Controller
{
    public function show_add_new_location()
    {
        $provinces = Province::orderBy('name', 'asc')->get();
        return View('admin/add_new_location', compact('provinces'));
    }
    public function show_list_locations()
    {
        $locations = Food_Locations::all();

        return View('admin/show_list_locations', compact('locations'));
    }
    public function getDetailLocation($locationSlug)
    {
        $location = Food_Locations::where('slug', $locationSlug)->first();
        if (empty($location) || $location->status == 1) {
            return view('error404');
        } else {
            $province = Province::find($location->province_id);
            $district = District::find($location->district_id);
            $ward = Ward::find($location->ward_id);
            $locationData = $location->toArray();
            $combinedData = array_merge($locationData, ['province' => $province, 'ward' => $ward, 'district' => $district]);
            return response()->json($combinedData);
        }
    }
    public function getListLocations(Request $request)
    {
        $perPage = $request->input('_limit', 8);
        $sort = $request->input('_sort_date', 'ASC');
        $page = $request->input('_page', 1);
        $district_id = $request->input('district_id');
        $province_id = $request->input('province_id');
        $ward_id = $request->input('ward_id');
        $query = Food_Locations::join('province', 'food_locations.province_id', '=', 'province.id')
            ->select('food_locations.*', 'province.name as province_name')
            ->leftJoin('district', 'food_locations.district_id', '=', 'district.id')
            ->select('food_locations.*', 'province.name as province_name', 'district.name as district_name')
            ->leftJoin('ward', 'food_locations.ward_id', '=', 'ward.id')
            ->select('food_locations.*', 'province.name as province_name', 'district.name as district_name', 'ward.name as ward_name')
            ->whereNotIn('food_locations.status', [1]);

        if ($request->has('searchContent')) {
            $searchContent = $request->input('searchContent');
            session(['searchContent' => $searchContent]);
            $query->where(function ($q) use ($searchContent) {
                $q->where('food_locations.name', 'like', '%' . $searchContent . '%')
                    ->orWhere('food_locations.description', 'like', '%' . $searchContent . '%');
            });
        }
        if ($province_id != null) {
            $query->where('food_locations.province_id', $province_id);
        }
        if ($district_id != null) {
            $query->where('food_locations.district_id', $district_id);
        }
        if ($ward_id != null) {
            $query->where('food_locations.ward_id', $ward_id);
        }
        $locations = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json($locations);
    }
    public function show_edit_location($itemId)
    {
        $location = Food_Locations::find($itemId);
        $province_old = $location->province;
        $district_old = $location->district;
        $ward_old = $location->ward;
        $provinces = Province::all();
        $districts = District::where('province_id', $province_old->id)->get();
        $wards = Ward::whereIn('district_id', $districts->pluck('id')->toArray())->get();
        return View('admin/show_edit_location', compact('location', 'province_old', 'district_old', 'ward_old', 'provinces', 'districts', 'wards', ));
    }
    public function get_district($province_id)
    {
        $districts = District::where('province_id', $province_id)->pluck('name', 'id');
        return response()->json($districts);
    }
    public function
        get_ward(
        $ward_id
    ) {
        $ward = Ward::where('district_id', $ward_id)->pluck('name', 'id');
        return response()->json($ward);
    }
    public function new_location(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'province_id' => 'required|exists:province,id',
            'district_id' => 'required|exists:district,id',
            'ward_id' => 'exists:ward,id',
            'time' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên địa điểm.',
            'name.max' => 'Tên địa điểm không được vượt quá 255 ký tự.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'province_id.required' => 'Vui lòng chọn Tỉnh/Thành Phố.',
            'province_id.exists' => 'Tỉnh/Thành Phố không hợp lệ.',
            'district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'district_id.exists' => 'Quận/Huyện không hợp lệ.',
            'ward_id.exists' => 'Phường/Xã không hợp lệ.',
            'time.required' => 'Vui lòng nhập thời gian.',
            'time.max' => 'Thời gian không được vượt quá 255 ký tự.',
            'contact_person.required' => 'Vui lòng nhập tên người liên hệ.',
            'contact_person.max' => 'Tên người liên hệ không được vượt quá 255 ký tự.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'contact_number.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'contact_number.max' => 'Số điện thoại liên hệ không được vượt quá 255 ký tự.',
            'image.required' => 'Vui lòng chọn ảnh.',
            'image.image' => 'Ảnh phải là hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg, gif hoặc webp.',
            'image.max' => 'Ảnh không được vượt quá 2MB.',
        ]);

        if ($validator->passes()) {
            $location = new Food_Locations();
            $location->name = $request->input('name');
            $location->description = $request->input('description');
            $location->province_id = $request->input('province_id');
            $location->district_id = $request->input('district_id');
            $location->ward_id = $request->input('ward_id');
            $location->time = $request->input('time');
            $location->contact_person = $request->input('contact_person');
            $location->address = $request->input('address');
            $location->contact_number = $request->input('contact_number');
            $location_slug = Str::slug($request->input('name')) . '_' . time();
            $location->slug = $location_slug;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $file_name = str::slug($request->input('name')) . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/food_locations'), $file_name);
                $location->image = '../../uploads/food_locations/' . $file_name;
            }

            $location->save();

            return response()->json(['message' => 'Thêm địa điểm phát thực phẩm thành công'], 200);
        }
        $errors = $validator->errors()->all();
        return response()->json(['errors' => $errors], 200);
    }

    public function block_location($itemId)
    {
        $locations = Food_Locations::find($itemId);
        if (!$locations) {
            return response()->json(['errors' => 'Not found this locations'], 404);
        }
        $locations->status = 1;
        $locations->save();
        return response()->json(['success' => 'location blocked successfully']);
    }
    public function unlock_location($itemId)
    {
        $locations = Food_Locations::find($itemId);
        if (!$locations) {
            return response()->json(['errors' => 'Not found this locations'], 404);
        }
        $locations->status = 0;
        $locations->save();
        return response()->json(['success' => 'location unblocked successfully']);
    }

    public function edit_location(request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'province_id' => 'required|exists:province,id',
            'district_id' => 'required|exists:district,id',
            'ward_id' => 'exists:ward,id',
            'time' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
        ], [
            'name.required' => 'Vui lòng nhập tên địa điểm.',
            'name.max' => 'Tên địa điểm không được vượt quá 255 ký tự.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
            'province_id.required' => 'Vui lòng chọn Tỉnh/Thành Phố.',
            'province_id.exists' => 'Tỉnh/Thành Phố không hợp lệ.',
            'district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'district_id.exists' => 'Quận/Huyện không hợp lệ.',
            'ward_id.exists' => 'Phường/Xã không hợp lệ.',
            'time.required' => 'Vui lòng nhập thời gian.',
            'time.max' => 'Thời gian không được vượt quá 255 ký tự.',
            'contact_person.required' => 'Vui lòng nhập tên người liên hệ.',
            'contact_person.max' => 'Tên người liên hệ không được vượt quá 255 ký tự.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'contact_number.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'contact_number.max' => 'Số điện thoại liên hệ không được vượt quá 255 ký tự.',
        ]);
        if ($validator->passes()) {
            $location = Food_Locations::find($request->id);

            if (!$location) {
                return response()->json(['error' => 'Không tìm thấy địa điểm để cập nhật'], 404);
            }
            if ($request->hasFile('image')) {
                $oldImagePath = $location->image;
                $parts = explode('/', $oldImagePath);
                $filename = end($parts);
                $oldImagePath = public_path('uploads/food_locations/' . $filename);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $file_name = Str::slug($location->name) . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/food_locations'), $file_name);
                $location->image = '../../uploads/food_locations/' . $file_name;
            }

            $location->name = $request->input('name');
            $location->description = $request->input('description');
            $location->province_id = $request->input('province_id');
            $location->district_id = $request->input('district_id');
            $location->ward_id = $request->input('ward_id');
            $location->time = $request->input('time');
            $location->contact_person = $request->input('contact_person');
            $location->address = $request->input('address');
            $location->contact_number = $request->input('contact_number');
            $location->save();
            return response()->json(['message' => 'Cập nhật địa điểm thành công thành công'], 200);
        }
        $errors = $validator->errors()->all();
        return response()->json(['errors' => $errors], 200);
    }
}
