@extends('admin/master')
@section('content')
<div class="card">
    <h5 class="card-header">Quản Lí Danh Sách Tài Khoản</h5>
    <form id="search-form" method="get" action="{{ route('show_manage_users') }}">
        @csrf
        <div class="row p-2">
            <div class="col-md-4 mb-2 mb-md-0">
                <input type="text" name="searchContent" class="form-control shadow-none" placeholder="Search..."
                    aria-label="Search..." value="{{ session('searchContent', '') }}" />
            </div>
            <div class="col-md-3 mb-2 mb-md-0"> <!-- Phân Quyền -->
                <select class="form-select" id="user_role" name="user_role" style="min-width:140px;">
                    <option value="null">Tất Cả Quyền</option>
                    <option value="1">Admin</option>
                    <option value="0">Người Dùng</option>
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0"> <!-- Trạng Thái -->
                <select class="form-select" id="user_status" name="user_status">
                    <option value="null">Tất Cả Trạng Thái</option>
                    <option value="1">Đã Xác Thực</option>
                    <option value="0">Chưa Xác Thực</option>
                    <option value="3">Đã Bị Khóa</option>
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
                    <th class="text-info text-nowrap text-center">Họ và tên</th>
                    <th class="text-info text-nowrap text-center">Email</th>
                    <th class="text-info text-nowrap text-center">Hình Ảnh</th>
                    <th class="text-info text-nowrap text-center">Phân Quyền</th>
                    <th class="text-info text-nowrap text-center">Trạng Thái</th>
                    <th class="text-info text-nowrap text-center">Thời Gian Tạo</th>
                    <th class="text-info text-nowrap text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($users as $user)
                <tr>
                    <td class="text-center">{{ $loop->index + 1 }}</td>
                    <td>
                        @if($user->full_name!=null)
                        {{$user->full_name}}
                        @else
                        null
                        @endif
                    </td>
                    <td>
                        {{$user->email}}
                    </td>
                    <td class="text-center">
                        @if($user->image!=null)
                        <img src="{{$user->image}}" alt="user" style="max-width:40px; max-height:40px;" alt
                            class="w-px-40 h-auto rounded-circle" height=50 width=50>
                        @else
                        <img style="max-width:40px; max-height:40px;" src="../../assets/img/avatars/1.png" alt
                            class="w-px-40 h-auto rounded-circle" height="50" width="50" />
                        @endif
                    </td>
                    <td class="text-center">
                        @if($user->role == 0)
                        <span class="badge bg-label-info me-1">Người Dùng</span>
                        @elseif($user->role ==1)
                        <span class="badge bg-label-success me-1">Admin</span>
                        @elseif($user->role == 3)
                        <span class="badge bg-label-danger me-1">Đã Bị Khóa</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($user->is_verified == 0)
                        <span class="badge bg-label-info me-1">Chưa Xác Thực</span>
                        @elseif( $user->is_verified ==1)
                        <span class="badge bg-label-success me-1">Đã Xác Thực</span>
                        @elseif( $user->is_verified == 3)
                        <span class="badge bg-label-danger me-1">Đã Bị Khóa</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $user->created_at }}</td>
                    <td class="text-center">
                        @if($user->is_verified!=3)
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('show_role_user', ['user_id' => $user->id]) }}">
                                    <i class="bx bx-user me-1"></i>Phân Quyền</a>
                                <button class="dropdown-item lock_user" data-item-id="{{$user->id}}"> <i
                                        class="bx bx-lock me-1"></i>Khóa Tài Khoản</button>
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
        <small class="text-light fw-semibold">Trang: {{ $users->currentPage() }}/{{ $users->lastPage() }}</small>
        <div class="demo-inline-spacing">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    @if ($users->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $users->previousPageUrl() }}" aria-label="Previous">
                            <i class="tf-icon bx bx-chevrons-left"></i>
                        </a>
                    </li>
                    @endif
                    @if ($users->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $users->previousPageUrl() }}" aria-label="Previous">
                            <i class="tf-icon bx bx-chevron-left"></i>
                        </a>
                    </li>
                    @endif
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if ($page == $users->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endif
                    @endforeach
                    @if ($users->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $users->nextPageUrl() }}" aria-label="Next">
                            <i class="tf-icon bx bx-chevron-right"></i>
                        </a>
                    </li>
                    @else
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                    </li>
                    @endif
                    @if ($users->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $users->url($users->lastPage()) }}" aria-label="Last">
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
                <h5 class="modal-title" id="exampleModalLabel1">Xác Nhận Khóa Tài Khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label text-warning">Bạn Có Chắc Chắn Muốn Khóa Tài Khoản Này?
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
    var lock_user_success = sessionStorage.getItem('lock_user_success');
    var role_success = sessionStorage.getItem('role_success');
    var role_user_success = sessionStorage.getItem('role_user_success');
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
        if (lock_user_success !== null) {
            var textContent = "Khóa Tài Khoản Thành Công";
            showToast(textContent, 'success');
            sessionStorage.removeItem('lock_user_success');
        }
        if (role_success !== null) {
            var textContent = "Phân Quyền Thành Công";
            showToast(textContent, 'success');
            sessionStorage.removeItem('role_success');
        }
        var lock_id = null;
        $(".lock_user").click(function () {
            var itemId = $(this).data("item-id");
            lock_id = itemId;
            $("#basicModal").modal("show");
        });
        $(".btn-confirm").click(function () {
            if (lock_id !== null) {
                $.ajax({
                    type: 'GET',
                    url: '/manage/user/lock/' + lock_id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.errors) {
                            showToast(response.errors, 'danger');
                        } else if (response.message) {
                            sessionStorage.setItem('lock_user_success', 'Khóa Tài Khoản Thành Công');
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