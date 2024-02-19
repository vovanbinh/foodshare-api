@extends('admin/master')
@section('content')
<div class="card">
    <h5 class="card-header">Danh Sách Báo cáo thực phẩm hỏng</h5>
    <div class="card-body">
        <div class="table-responsive" style="min-height:500px;">
            <table id="notifications" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center align-middle">#</th>
                        <th class="text-center align-middle">Tên Người Gửi</th>
                        <th class="text-center align-middle">Food Id</th>
                        <th class="text-center align-middle">Tên Thực Phẩm</th>
                        <th class="text-center align-middle">Trạng thái thực phẩm</th>
                        <th class="text-center align-middle">Nội dung</th>
                        <th class="text-center align-middle">Thời Gian Tạo</th>
                        <th class="text-center align-middle">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifications as $notification)
                    <tr>
                        <td class="text-center ">{{ $loop->index+ 1 }}</td>
                        <td>{{$notification->user->full_name }}</td>
                        <td class="text-center ">{{$notification->food->id }}</td>
                        <td class="text-center ">{{$notification->food->title }}</td>
                        <td class="text-center ">
                            @if($notification->food->status == 0)
                            <span class="badge bg-label-info me-1">Đang Mở</span>
                            @elseif($notification->food->status == 1)
                            <span class="badge bg-label-success me-1">Đã Có Người Nhận</span>
                            @elseif($notification->food->status == 2)
                            <span class="badge bg-label-warning me-1">Đã Dừng Tặng</span>
                            @elseif($notification->food->status == 4)
                            <span class="badge bg-label-danger me-1">Đã Bị Khóa</span>
                            @endif
                        </td>
                        <td>{{ $notification->message}}</td>
                        <td>{{$notification->created_at }}</td>
                        <td class="text-center ">
                            <a href="/manage/donated/{{$notification->food->id}}">Xem thực phẩm</a>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function () {
        new DataTable('#notifications');
    })
</script>
@endsection