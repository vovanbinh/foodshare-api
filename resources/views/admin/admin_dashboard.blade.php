@extends('admin/master')
@section('content')
<div class="col-lg-12 mb-4 order-0">
    <div class="card">
    <div class="d-flex align-items-end row">
        <div class="col-sm-7">
        <div class="card-body">
            <h5 class="card-title text-primary">Congratulations Admin! 🎉</h5>
            <p class="mb-4">
            We have <span class="fw-bold text-danger">{{$transactionCount}}</span> more transactions today.
            </p>
            <a href="{{ route('show_manage_transactions') }}" class="btn btn-sm btn-outline-primary">View detail</a>
        </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
        <div class="card-body pb-0 px-0 px-md-4">
            <img src="../assets/img/illustrations/man-with-laptop-light.png" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
        </div>
        </div>
    </div>
    </div>
</div>
<div class="col-lg-12 col-md-4 order-1">
    <div class="row">
        <div class="col-lg-4 col-md-12 col-6 mb-4">
            <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                    <img src="../assets/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded">
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                    <a class="dropdown-item" href="{{ route('show_manage_donated') }}">View More</a>
                    </div>
                </div>
                </div>
                <span>Tổng Thực Phẩm Tặng</span>
                <h3 class="card-title text-bold text-success mb-1">{{$foodCount}}</h3>
            </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-6 mb-4">
            <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                    <img src="../assets/img/icons/unicons/chart-success.png" alt="chart success" class="rounded">
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                    <a class="dropdown-item" href="{{ route('show_manage_transactions') }}">View More</a>
                    </div>
                </div>
                </div>
                <span>Tổng Giao Dịch</span>
                <h3 class="card-title text-bold text-success mb-1">{{$transactionCount}}</h3>
            </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-6 mb-4">
            <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                    <img src="../assets/img/icons/unicons/cc-warning.png" alt="User" class="rounded">
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                    <a class="dropdown-item" href="{{ route('show_manage_users') }}">View More</a>
                    </div>
                </div>
                </div>
                <span>Tổng Tài Khoản</span>
                <h3 class="card-title text-bold text-success mb-1">{{$userCount}}</h3>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
</script>
@endsection