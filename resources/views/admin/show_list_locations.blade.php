@extends('admin/master')
@section('content')
<div class="card">
    <h5 class="card-header">List Employees</h5>
    <div class="card-body">
        <div class="table-responsive" style="min-height:500px;">
            <table id="locations" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center align-middle">#</th>
                        <th class="text-center align-middle">Tên Điểm Phát Thực Phẩm</th>
                        <th class="text-center align-middle">Hình Ảnh</th>
                        <th class="text-center align-middle">Tên Người Đại Diện</th>
                        <th class="text-center align-middle">Thông Tin Liên Hệ</th>
                        <th class="text-center align-middle">Mô Tả</th>
                        <th class="text-center align-middle">Thời Gian Phát Trong Tuần</th>
                        <th class="text-center align-middle">Thời Gian Tạo</th>
                        <th class="text-center align-middle">Trạng Thái</th>
                        <th class="text-center align-middle">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($locations as $location)
                    <tr>
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $location->name }}</td>
                        <td class="text-center">
                            <img src="{{ $location->image}}" alt="location" class="rounded-circle" height=50 width=50>
                        </td>
                        <td class="text-center ">{{ $location->contact_person }}</td>
                        <td class="text-center">{{ $location->contact_number }}</td>
                        <td>{{ $location->description}}</td>
                        <td>{{ $location->time}}</td>
                        <td class="text-center">{{ $location->created_at }}</td>
                        <td class="text-center">
                            @if($location->status ==1)
                            <span class="badge bg-label-warning me-1">Đang Khóa</span>
                            @else
                            <span class="badge bg-label-success me-1">Đang Mở</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a href="/location/edit-location/{{$location->id}}"
                                        class="dropdown-item text-success"></i>Chỉnh Sửa</a>
                                    @if($location->status!=1)
                                    <button type="button" id="" class="dropdown-item text-danger show_model"
                                        data-item-id="{{$location->id}}">
                                        Khóa</button>
                                    @elseif($location->status == 1)
                                    <button type="button" id="" class="dropdown-item text-warning show_model_un"
                                        data-item-id="{{$location->id}}">
                                        Mở Khóa</button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- model -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Xác Nhận Khóa Điểm Phát Thực Phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label text-warning">Bạn có thực sự muốn khóa điểm phát thực
                            phẩm này?
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-confirm btn-primary" data-bs-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="basicModalUn" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Xác Nhận Mở Khóa Điểm Phát Thực Phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label for="nameBasic" class="form-label text-warning">Bạn có thực sự muốn mở khóa điểm phát
                            thực
                            phẩm này?
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-confirm-un btn-primary" data-bs-dismiss="modal">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<!-- toast -->
<div class="bs-toast toast toast-placement-ex m-2 fade bg-warning top-0 end-0" role="alert" aria-live="assertive"
    aria-atomic="true" data-delay="2000">
    <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-semibold">FoodShare</div>
        <small>now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body"></div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function () {
        new DataTable('#locations');
        var success = sessionStorage.getItem('success');
        var success_un = sessionStorage.getItem('success_un');

        function showToast(message, type) {
            var toast = document.querySelector('.bs-toast');
            var toastBody = toast.querySelector('.toast-body');
            toast.classList.remove('bg-success', 'bg-danger', 'bg-warning');
            toast.classList.add('bg-' + type);
            toastBody.textContent = message;
            var bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }
        if (success !== null) {
            var textContent = "Khóa Điểm Phát Thực Phẩm Thành Công";
            showToast(textContent, 'success');
            sessionStorage.removeItem('success');
        }
        if (success_un !== null) {
            var textContent = "Mở Khóa Điểm Phát Thực Phẩm Thành Công";
            showToast(textContent, 'success');
            sessionStorage.removeItem('success_un');
        }
        //show model
        var itemId = null;
        $(".show_model").click(function () {
            var itemIdModel = $(this).data("item-id");
            itemId = itemIdModel;
            $("#basicModal").modal("show");
        });
        var itemId_un = null;
        $(".show_model_un").click(function () {
            var itemIdModel = $(this).data("item-id");
            itemId_un = itemIdModel;
            $("#basicModalUn").modal("show");
        });
        $(".btn-confirm").click(function () {
            if (itemId !== null) {
                $.ajax({
                    type: 'GET',
                    url: '/block-location/' + itemId,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            sessionStorage.setItem('success', 'success');
                            window.location.reload();
                        } else {
                            showToast(response.errors, 'danger');
                        }
                    },
                    error: function (error) {
                        if (error.status === 422) {
                            var errors = error.responseJSON.errors;
                            showToast(errors, 'danger');
                        }
                    }
                });
            }
        });
        $(".btn-confirm-un").click(function () {
            if (itemId_un !== null) {
                $.ajax({
                    type: 'GET',
                    url: '/unlock-location/' + itemId_un,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            sessionStorage.setItem('success_un', 'success');
                            window.location.reload();
                        } else {
                            showToast(response.errors, 'danger');
                        }
                    },
                    error: function (error) {
                        if (error.status === 422) {
                            var errors = error.responseJSON.errors;
                            showToast(errors, 'danger');
                        }
                    }
                });
            }
        });
    })
</script>
@endsection