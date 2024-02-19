@extends('admin/master')
@section('content')
<div class="card">
    <h5 class="card-header">Danh Sách Giao Dịch</h5>
    <form id="search-form" method="get" action="{{ route('show_manage_transactions') }}">
        @csrf
        <div class="row p-2">
            <div class="col-md-4 mb-2 mb-md-0">
                <input type="text" name="searchContent" class="form-control shadow-none" placeholder="Search..."
                    aria-label="Search..." value="{{ session('searchContent', '') }}" />
            </div>
            <div class="col-md-4 mb-2 mb-md-0"> <!-- Trạng Thái -->
                <select class="form-select" id="transaction_status" name="transaction_status">
                    <option value="null">Tất Cả Trạng Thái</option>
                    <option value="1">Đã Lấy</option>
                    <option value="0">Đang Đợi Xác Nhận</option>
                    <option value="2">Đã Hủy Nhận</option>
                    <option value="3">Hết Thời Gian Nhận</option>
                    <option value="4">Thực Phẩm Bị Khóa</option>
                </select>
            </div>
            <div class="col-md-4 "> <!-- Tìm kiếm -->
                <button type="submit" class="btn btn-info">Tìm</button>
            </div>
        </div>
    </form>
    <div class="table-responsive" style="min-height:500px;">
        <table class="table" id="donated-table">
            <thead class="table-dark">
                <tr>
                    <th class="text-info text-nowrap text-center">STT</th>
                    <th class="text-info text-nowrap text-center">Người Nhận</th>
                    <th class="text-info text-nowrap text-center">Người Tặng</th>
                    <th class="text-info text-nowrap text-center">Tên Thực Phẩm</th>
                    <th class="text-info text-nowrap text-center">Hình Ảnh</th>
                    <th class="text-info text-nowrap text-center">Số Lượng</th>
                    <th class="text-info text-nowrap text-center">Trạng Thái</th>
                    <th class="text-info text-nowrap text-center">Thời Gian Tạo</th>
                    <th class="text-info text-nowrap text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach($transactions as $transaction)
                <tr>
                    <td class="text-center">{{ $loop->index + 1 }}</td>
                    <td class="">{{ $transaction->receiver->full_name }}</td>
                    <td class="text-center">
                        {{ $transaction->food->user->full_name }}
                    </td>
                    <td>
                        {{ $transaction->food->title }}
                    </td>
                    <td class="text-center">
                        <img src="{{$transaction->food->images[0]->image_url}}" alt="food"
                            style="min-width:40px; min-height:40px; max-width:40px; max-height:40px;" alt class="w-px-40 h-auto rounded-circle" height=50
                            width=50>
                    </td>
                    <td class="text-center">
                        {{$transaction->quantity_received }}
                    </td>
                    <td class="text-center">
                        @if($transaction->status == 0)
                        <span class="badge bg-label-warning me-1">Đang Đợi Xác Nhận</span>
                        @elseif($transaction->status ==1)
                        <span class="badge bg-label-success me-1">Đã Lấy</span>
                        @elseif($transaction->status == 2)
                        <span class="badge bg-label-danger me-1">Đã Hủy Nhận</span>
                        @elseif($transaction->status == 3)
                        <span class="badge bg-label-danger me-1">Hết Thời Gian Nhận</span>
                        @elseif($transaction->status == 4)
                        <span class="badge bg-label-danger me-1">Thực Phẩm Bị Khóa</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $transaction->created_at }}</td>
                    <td class="text-center"><a src="Chi tiết thực phẩm" href="/manage/donated/{{$transaction->food->id}}">Xem Thực Phẩm</a></td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="row mt-2">
    <div class="col text-center">
        <small class="text-light fw-semibold">Trang: {{ $transactions->currentPage() }}/{{ $transactions->lastPage()
            }}</small>
        <div class="demo-inline-spacing">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    @if ($transactions->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevrons-left"></i></span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $transactions->previousPageUrl() }}" aria-label="Previous">
                            <i class="tf-icon bx bx-chevrons-left"></i>
                        </a>
                    </li>
                    @endif
                    @if ($transactions->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevron-left"></i></span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $transactions->previousPageUrl() }}" aria-label="Previous">
                            <i class="tf-icon bx bx-chevron-left"></i>
                        </a>
                    </li>
                    @endif
                    @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                    @if ($page == $transactions->currentPage())
                    <li class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endif
                    @endforeach
                    @if ($transactions->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $transactions->nextPageUrl() }}" aria-label="Next">
                            <i class="tf-icon bx bx-chevron-right"></i>
                        </a>
                    </li>
                    @else
                    <li class="page-item disabled">
                        <span class="page-link"><i class="tf-icon bx bx-chevron-right"></i></span>
                    </li>
                    @endif
                    @if ($transactions->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $transactions->url($transactions->lastPage()) }}"
                            aria-label="Last">
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
@endsection
@section('js')
@endsection