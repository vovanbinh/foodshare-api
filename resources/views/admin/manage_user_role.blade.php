@extends('admin/master')

@section('content')
<div class="card mb-4">
    <h5 class="card-header text-warning text-wrap">Phân Quyền Người Dùng "{{$user->full_name}}"</h5>
    <p id="user_id" data-user-id="{{$user->id}}" hidden></p>
    <div class="card-body">
        <div class="row gy-3">
            <div class="col-md">
                <label class="text-danger">Vui lòng kiểm tra đầy đủ thông tin trước khi phân quyền!</label>
                <div class="form-check mt-3">
                    <input name="default-radio-1" class="form-check-input" type="radio" value="1" id="defaultRadio1" @if($user->role==1) checked="" @endif >
                    <label class="form-check-label" for="defaultRadio1"> Quyền Admin </label>
                </div>
                <div class="form-check">
                    <input name="default-radio-1" class="form-check-input" type="radio" value="0" id="defaultRadio2" @if($user->role==0) checked="" @endif >
                    <label class="form-check-label" for="defaultRadio2"> Quyền Người Dùng </label>
                </div>
            </div>
        </div>
        <div class="demo-inline-spacing">
            <button type="button" id="btn_update" class="btn btn-primary">Cập Nhật</button>
            <a href="/manage/users" type="button" class="btn btn-secondary">Trở Về</a>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var btnUpdate = document.getElementById('btn_update');
        btnUpdate.addEventListener('click', function () {
            var user_id = document.querySelector('#user_id').getAttribute('data-user-id');
            var radioInput = document.querySelector('input[name="default-radio-1"]:checked'); 
            var role = radioInput ? radioInput.value : null;
            $.ajax({
                type: 'GET',
                url: '/manage/user-role/' + user_id + '/' + role, 
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                dataType: 'json',
                success: function (response) {
                    if (response.errors) {
                        showToast(response.errors, 'danger');
                    } else if (response.message) {
                        sessionStorage.setItem('role_success', 'Phân Quyền Thành Công');
                        window.location.href = '/manage/users'; 
                    }
                },
                error: function (error) {
                    if (error.status === 422) {
                        var errors = error.responseJSON.errors;
                        showToast(errors, 'danger');
                    }
                }
            });
        });
    });
</script>
@endsection
