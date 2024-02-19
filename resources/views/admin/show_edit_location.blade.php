@extends('admin/master')
@section('content')
<div class="col-md-12">
    <div class="card mb-4">
        <form id="form_add_location" enctype="multipart/form-data">
            @csrf
            <h5 class="card-header">Sửa địa điểm phát thực phẩm</h5>
            <input hidden name="id" type="text" value="{{$location->id}}">
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên điểm phát thực phẩm</label>
                    <input type="text" name="name" value="{{$location->name}}" class="form-control"
                        placeholder="Nhập tên điểm phát thực phẩm" />
                </div>
                <div class="mb-3">
                    <label for="contact_person" class="form-label">Tên người đại diện</label>
                    <input type="text" name="contact_person" class="form-control" value="{{$location->contact_person}}"
                        placeholder="Nhập Tên người đại diện" />
                </div>
                <div class="mb-3">
                    <label for="contact_number" class="form-label">Thông tin liên hệ (SDT hoặc đường link mạng xã
                        hội)</label>
                    <input type="text" value="{{$location->contact_number}}" name="contact_number" class="form-control"
                        placeholder="Nhập SDT liên hệ" />
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="3">{{$location->description}}</textarea>
                </div>
                <div class="mb-3">
                    <label for="time" class="form-label">Thời Gian Phát Trong Tuần (ví dụ: 12 giờ trưa mỗi ngày)</label>
                    <input type="text" value="{{$location->time}}" name="time" class="form-control"
                        placeholder="Nhập thời gian phát thực phẩm" />
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Địa điểm (Số nhà, tên đường, ví dụ: 127 Phạm Văn
                        Sảo)</label>
                    <input type="text" name="address" value="{{$location->address}}" class="form-control"
                        placeholder="Nhập địa điểm phát thực phẩm" />
                </div>
                <div class="mb-3">
                    <label for="location_id" class="col-md-2 col-form-label">Địa Điểm Nhận</label>
                    <div id="defaultFormControlHelp" class="form-text">Tỉnh/Thành phố</div>
                    <select style=" max-height: 200px; overflow-y: auto;" class="form-select" name="province_id"
                        aria-label="Default select example">
                        @foreach($provinces as $province)
                        <option value="{{ $province->id }}" @if($province->id == $province_old->id) selected @endif>{{
                            $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <div id="defaultFormControlHelp" class="form-text">Quận/Huyện</div>
                    <select style=" max-height: 200px; overflow-y: auto;" class="form-select" name="district_id"
                        aria-label="Default select example">
                        @foreach($districts as $district)
                        <option value="{{ $district->id }}" @if($district_old && $district->id == $district_old->id)
                            selected @endif>{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <div id="defaultFormControlHelp" class="form-text">Phường/Xã</div>
                    <select style=" max-height: 200px; overflow-y: auto;" class="form-select" name="ward_id"
                        aria-label="Default select example">
                        @foreach($wards as $ward)
                        <option value="{{ $ward->id }}" @if($ward_old && $ward->id == $ward_old->id) selected @endif>{{
                            $ward->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="image" class="col-md-5 col-form-label">Ảnh Địa Điểm</label>
                    <input type="file" name="image" class="form-control" id="inputImage" />
                </div>
                <div class="mb-3 row">
                    <div class="col-5">
                        <p>Ảnh Hiện Tại</p>
                        <img alt="Ảnh Địa điểm" class="col-4" style="border-radius:1em;" src="{{$location->image}}">
                    </div>
                    <div class="col-5">

                        <div id="image" class="col-12 pt-4">

                        </div>
                    </div>
                </div>
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-success">Sửa Địa Điểm</button>
                </div>
            </div>
        </form>
        <!-- toast -->
        <div class="bs-toast toast toast-placement-ex m-2 fade bg-warning top-0 end-0" role="alert"
            aria-live="assertive" aria-atomic="true" data-delay="2000">
            <div class="toast-header">
                <i class="bx bx-bell me-2"></i>
                <div class="me-auto fw-semibold">FoodShare</div>
                <small>now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    const inputImage = document.getElementById('inputImage');
    const imageContainer = document.getElementById('image');
    const maxWidth = 200;
    inputImage.addEventListener('change', function (event) {
        const selectedImage = event.target.files[0];
        if (selectedImage) {
            const imgElement = document.createElement('img');
            imgElement.src = URL.createObjectURL(selectedImage);
            imgElement.classList.add('max-width-image'); // Thêm lớp CSS cho phần tử img
            imgElement.style.maxWidth = maxWidth + 'px'; // Đặt giới hạn chiều rộng thông qua inline CSS
            imgElement.style.borderRadius = 1 + 'em';
            while (imageContainer.firstChild) {
                imageContainer.removeChild(imageContainer.firstChild);
            }
            imageContainer.appendChild(imgElement);
        }
    });
    $(document).ready(function () {
        function showToast(message, type) {
            var toast = document.querySelector('.bs-toast');
            var toastBody = toast.querySelector('.toast-body');
            toast.classList.remove('bg-success', 'bg-danger', 'bg-warning');
            toast.classList.add('bg-' + type);
            toastBody.textContent = message;
            var bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }

        $('#form_add_location').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: '/location/edit-location',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function (response) {
                    if (response.errors) {
                        response.errors.reverse().forEach(function (errorMessage) {
                            showToast(errorMessage, 'danger');
                        });
                    } else if (response.message) {
                        showToast(response.message, 'success');
                    }
                },
                error: function (error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                    }
                }
            });
        });
    });
</script>
<script>
    document.querySelector('select[name="province_id"]').addEventListener('change', function () {
        var selectedProvince = this.value;
        $.ajax({
            url: "/get-district/" + selectedProvince,
            method: "GET",
            dataType: "json",
            success: function (data) {
                var districtSelect = $('select[name="district_id"]');
                districtSelect.empty();
                districtSelect.append('<option value="">Chọn quận/huyện</option>');
                $.each(data, function (key, value) {
                    districtSelect.append('<option value="' + key + '">' + value + '</option>');
                });
                var wardSelect = $('select[name="ward_id"]');
                wardSelect.empty();
            }
        });

    });
</script>
<script>
    document.querySelector('select[name="district_id"]').addEventListener('change', function () {
        var selectedDistrict = this.value;
        $.ajax({
            url: "/get-ward/" + selectedDistrict,
            method: "GET",
            dataType: "json",
            success: function (data) {
                var wardSelect = $('select[name="ward_id"]');
                wardSelect.empty();
                wardSelect.append('<option value="">Chọn phường/xã</option>');
                $.each(data, function (key, value) {
                    wardSelect.append('<option value="' + key + '">' + value + '</option>');
                });
            }
        });
    });
</script>
@endsection