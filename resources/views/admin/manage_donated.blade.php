@extends('admin/master')
@section('content')
<div class="card">
    <h5 class="card-header">Quản Lí Danh Sách Thực Phẩm Tặng</h5>
    <form id="search-form" method="get" action="{{ route('show_manage_donated') }}">
        @csrf
        <div class="row p-2">
            <div class="col-md-4 mb-2 mb-md-0">
                <input type="text" name="searchContent" class="form-control shadow-none" placeholder="Search..."
                    aria-label="Search..." value="{{ session('searchContent', '') }}" />
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <select class="form-select" name="category_id" style="min-width:140px;">
                    <option value="null">Tất Cả Danh Mục</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ session('category_id', '' )==$category->id ? 'selected' : ''
                        }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0"> <!-- Trạng Thái -->
                <select class="form-select" name="food_status" id="food_status">
                    <option value="null">Tất Cả Trạng Thái</option>
                    <option value="1">Đã Có Người Nhận</option>
                    <option value="2">Đã Dừng Tặng</option>
                    <option value="4">Đã Bị Khóa</option>
                </select>
            </div>
            <div class="col-md-2"> <!-- Tìm kiếm -->
                <button type="submit" class="btn btn-info text-start">Tìm</button>
            </div>
        </div>
    </form>

    <div class="table-responsive" style="min-height:500px;">
        <table class="table" id="donated-table">
            <thead class="table-dark">
                <tr>
                    <th class="text-info text-nowrap text-center">STT</th>
                    <th class="text-info text-nowrap text-center">Người Tặng</th>
                    <th class="text-info text-nowrap text-center">Tên Thực Phẩm</th>
                    <th class="text-info text-nowrap text-center">Hình Ảnh</th>
                    <th class="text-info text-nowrap text-center">Số Lượng Còn</th>
                    <th class="text-info text-nowrap text-center">Thời Gian Tặng</th>
                    <th class="text-info text-nowrap text-center">Trạng Thái</th>
                    <th class="text-info text-nowrap text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($foods as $food)
                <tr>
                    <td class="text-center">{{ $loop->index + 1 }}</td>
                    <td class="text-center">{{ $food->user->full_name }}</td>
                    <td>{{ $food->title}} </td>
                    <td class="text-center">
                        <img src="{{ $food->images[0]->image_url }}" alt="food" class="rounded-circle" height=50
                            width=50>
                    </td>
                    <td class="text-center">{{ $food->quantity}} </td>
                    <td class="text-center">{{ $food->created_at}} </td>
                    <td class="text-center">
                        @if($food->status == 0)
                        <span class="badge bg-label-info me-1">Đang Mở</span>
                        @elseif($food->status == 1)
                        <span class="badge bg-label-success me-1">Đã Có Người Nhận</span>
                        @elseif($food->status == 2)
                        <span class="badge bg-label-warning me-1">Đã Dừng Tặng</span>
                        @elseif($food->status == 4)
                        <span class="badge bg-label-danger me-1">Đã Bị Khóa</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($food->status!=4)
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                    href="{{ route('manage_donated_detail',['food_donated_id' => $food->id]) }}"> <i
                                        class="bx bx-game me-1"></i>Xem Chi Tiết</a>
                                <button class="dropdown-item lock_donated" data-item-id="{{$food->id}}"> <i
                                        class="bx bx-lock me-1"></i>Khóa Tặng</button>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="row mt-2">
    <div class="col text-center">
        <small class="text-light fw-semibold">Trang: {{ $foods->currentPage() }}/{{ $foods->lastPage() }}</small>
        <div class="demo-inline-spacing">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    @if ($foods->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $foods->previousPageUrl() }}" aria-label="Previous">
                            <i class="tf-icon bx bx-chevrons-left"></i>
                        </a>
                    </li>
                    @endif
                    @if ($foods->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $foods->previousPageUrl() }}" aria-label="Previous">
                            <i class="tf-icon bx bx-chevron-left"></i>
                        </a>
                    </li>
                    @endif
                    @foreach ($foods->getUrlRange(1, $foods->lastPage()) as $page => $url)
                    @if ($page == $foods->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endif
                    @endforeach
                    @if ($foods->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $foods->nextPageUrl() }}" aria-label="Next">
                            <i class="tf-icon bx bx-chevron-right"></i>
                        </a>
                    </li>
                    @else
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                    </li>
                    @endif
                    @if ($foods->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $foods->url($foods->lastPage()) }}" aria-label="Last">
                            <i class="tf-icon bx bx-chevrons-right"></i>
                        </a>
                    </li>
                    @else
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevrons-right"></i></span>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- model -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Xác Nhận Khóa Tặng Thực Phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label text-warning">Bạn Có Chắc Chắn Muốn Khóa Thực Phẩm Này?
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-confirm btn-primary" data-bs-dismiss="modal">Xác Nhận</button>
            </div>
        </div>
    </div>
</div>
<!-- {{-- Toast --}} -->
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
    var lock_success = sessionStorage.getItem('lock_success');
    function showToast(message, type) {
        var toast = document.querySelector('.bs-toast');
        var toastBody = toast.querySelector('.toast-body');
        toast.classList.remove('bg-success', 'bg-danger', 'bg-warning');
        toast.classList.add('bg-' + type);
        toastBody.textContent = message;
        var bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
    $(document).ready(function () {
        if (lock_success !== null) {
            var textContent = "Khóa Thực Phẩm Thành Công";
            showToast(textContent, 'success');
            sessionStorage.removeItem('lock_success');
        }
        var lock_id = null;
        $(".lock_donated").click(function () {
            var itemId = $(this).data("item-id");
            console.log(itemId);
            lock_id = itemId;
            $("#basicModal").modal("show");
        });
        $(".btn-confirm").click(function () {
            if (lock_id !== null) {
                $.ajax({
                    type: 'GET',
                    url: '/manage/lock-donated/' + lock_id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.errors) {
                            showToast(response.errors, 'danger');
                        } else if (response.message) {
                            sessionStorage.setItem('lock_success', 'Khóa Thực Phẩm Thành Công');
                            window.location.reload();
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
    });
</script>
@endsection