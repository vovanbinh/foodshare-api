@extends('admin/master')
@section('content')
<div class="row bg-white" style="border-radius: 0.5rem;">
  <div class="col-md-4 pb-3">
    <div class="row">
      <div class="image-slider p-1">
        <img id="current-image"
          style="width: 420px;  height: 450px; border-radius:2%; object-fit: cover; object-position: center center;"
          src="{{$imageUrls[0]}}">
      </div>
    </div>
    <div class="row center ml-2 ">
      <div class="thumbnails">
        <button type="button" id="prev-thumbnail" class="btn btn-info p-1">&lt;</button>
        <div class="thumbnail-images">
          @foreach ($imageUrls as $imageUrl)
          <img class="thumbnail" src="{{$imageUrl}}">
          @endforeach
        </div>
        <button type="button" id="next-thumbnail" class="btn btn-info btn-lg p-1"
          style="margin-left:10px;">&gt;</button>
      </div>
    </div>
  </div>
  <div class="col-md-8 text-start">
    <div class="mb-2">
      <div class="pt-1 mb-2">
        <div class="row">
          <div class="col-lg-1">
            @if($user->image!=null)
            <img src="{{$user->image}}" alt="user-avatar" class="rounded" height="50" width="50">
            @else
            <img src="../../assets/img/avatars/1.png" alt="user-avatar" class="rounded" height="50" width="50">
            @endif
          </div>
          <div class="col-lg-11">
            <p class="text mb-0 ">Người Tặng: {{$user->full_name}}</p>
            <div class="col-lg-11">
              @php
              $averageRating = null;
              $totalRating = 0;
              $totalRatings = 0;
              foreach ($ratings as $rating) {
              if (!is_null($rating)) {
              $totalRating += $rating->rating;
              $totalRatings++;
              }
              }
              if ($totalRatings > 0) {
              $averageRating = $totalRating / $totalRatings;
              }
              @endphp
              <p class="text mb-0">
                Điểm Đánh Giá:
                @if($averageRating>0)
                {{$averageRating}}
                @endif
              <p class="text mb-0">
                @if ($averageRating >= 1 && $averageRating < 2) <i class="bx bx-star me-1"></i>
                  @elseif ($averageRating >= 2 && $averageRating < 3) <i class="bx bx-star me-1"></i><i
                      class="bx bx-star me-1"></i>
                    @elseif ($averageRating >= 3 && $averageRating < 4) <i class="bx bx-star me-1"></i><i
                        class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i>
                      @elseif ($averageRating >= 4 && $averageRating < 5) <i class="bx bx-star me-1"></i><i
                          class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i>
                        @elseif ($averageRating >= 5)
                        <i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i><i
                          class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i>
                        @else
                        Chưa có đánh giá
                        @endif
              </p>
              </p>
            </div>
          </div>
        </div>
      </div>
      <h5 class="pt-3">Thông Tin Thực Phẩm</h5>
    </div>
    <hr class="my-0">
    <div class="col-md-12">
      <div class="row mt-3">
        <div class="col-md-3">
          <strong class="text-info">Tên Thực Phẩm: </strong>
        </div>
        <div class="col-md-9 text-info " style="padding-left:2em">
          {{$foodData['title']}} <strong class="text-success">Free</strong></p>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-3">
          <strong>Địa chỉ nhận: </strong>
        </div>
        <div class="col-md-9" style="padding-left:2em">
          {{$formatted_address}}
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-3">
          <strong>Mô Tả:</strong>
        </div>
        <div class="col-md-9" style="padding-left:2em">
          {{$foodData['description']}}
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-3">
          <strong>Đăng Lúc:</strong>
        </div>
        <div class="col-md-9" style="padding-left:2em">
          {{ date('Y-m-d H:i:s', strtotime($foodData['created_at'])) }}
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-3">
          <strong>Hạn Thực Phẩm:</strong>
        </div>
        <div class="col-md-9" style="padding-left:2em">
          {{$foodData['expiry_date']}}
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-3">
          <strong>Thời gian cho phép lấy thực phẩm sau khi xác nhận:</strong>
        </div>
        <div class="col-md-9" style="padding-left:2em">
          @if ($foodData['remaining_time_to_accept']==30)
          30 Phút
          @endif
          @if ($foodData['remaining_time_to_accept']== 60)
          1 tiếng
          @endif
          @if ($foodData['remaining_time_to_accept']==90)
          1 tiếng 30 phút
          @endif
          @if ($foodData['remaining_time_to_accept']==120)
          2 tiếng
          @endif
          @if ($foodData['remaining_time_to_accept']==150)
          2 tiếng 30 phút
          @endif
          @if ($foodData['remaining_time_to_accept']==180)
          3 tiếng
          @endif
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-3">
          <strong>Danh mục:</strong>
        </div>
        <div class="col-md-9" style="padding-left:2em">
          @foreach($categories as $categorie)
          @if($categorie->id == $foodData['category_id'])
          {{ $categorie->name }}
          @endif
          @endforeach
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-3">
          <strong>Số Lượng:</strong>
        </div>
        <div class="col-md-9" style="padding-left:2em">
          {{$foodData['quantity']}}
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-3">
          <strong>Trạng thái:</strong>
        </div>
        <div class="col-md-9" style="padding-left:2em">
          @if($foodData['status'] == 0)
          Đang mở
          @elseif($foodData['status'] == 1)
          Đã có người nhận
          @elseif($foodData['status'] == 2)
          Đã dừng tặng
          @elseif($foodData['status'] == 4)
          Đã bị khóa
          @endif
        </div>
      </div>
      <div class="row mb-3">
        <a href="/manage/donated" type="button" class="btn btn-primary col-2"
          style="margin-left:15px; min-width:100px;">Trở Về</a>
        @if( $foodData['status'] != 2 && $foodData['status'] != 4)
        <button class="btn btn-warning lock_donated" style="max-width:150px; margin-Left:10px"
          data-item-id="{{$foodData['id']}}"> <i class="bx bx-lock me-1"></i>Khóa
          Tặng</button>
        @endif
      </div>
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
    </div>
  </div>
  @if ($ratings !== null && count($ratings) > 0)
  <div class="row" style="margin-left:0.05em;">
    <div class="col-lg-12">
      <h5>Đánh Giá</h5>
      @foreach ($ratings as $rating)
      <div class="mb-3">
        @if (!is_null($rating))
        <div class="card">
          <div class="d-flex align-items-end row">
            <div class="col-sm-7">
              <div class="card-body">
                <h5 class="card-title">Người Nhận Thực Phẩm
                  <h5 class="card-title text-primary">
                    @if($rating->rating == 1)
                    <i class="bx bx-star me-1"></i>
                    @elseif($rating->rating == 2)
                    <i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i>
                    @elseif($rating->rating == 3)
                    <i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i>
                    @elseif($rating->rating == 4)
                    <i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i><i
                      class="bx bx-star me-1"></i>
                    @elseif($rating->rating == 5)
                    <i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i><i
                      class="bx bx-star me-1"></i><i class="bx bx-star me-1"></i>
                    @endif
                  </h5>
                  <p class="mb-2">
                    Nội Dung:
                    @if($rating->review !=null)
                    {{ $rating->review }}
                    @else
                    không có nội dung!
                    @endif
                  </p>
                  <p class="mb-2">
                    {{$rating->created_at}}
                  </p>
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
      @endforeach
    </div>
  </div>
  @else
  <p>Chưa có đánh giá nào.</p>
  @endif
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
  const thumbnailImages = <? php echo json_encode($imageUrls); ?>;
  const currentImage = document.getElementById('current-image');
  const thumbnailImagesContainer = document.querySelector('.thumbnail-images');
  const prevThumbnailButton = document.getElementById('prev-thumbnail');
  const nextThumbnailButton = document.getElementById('next-thumbnail');
  let startIndex = 0;

  function showMainImage() {
    currentImage.src = '{{$imageUrls[0]}}';
  }

  function showThumbnailImage(index) {
    currentImage.src = (thumbnailImages[index]) ? thumbnailImages[index] : '{{$imageUrls[0]}}';
  }

  showMainImage(); // Hiển thị hình ảnh chính ban đầu

  function updateThumbnailImages() {
    thumbnailImagesContainer.innerHTML = '';
    if (thumbnailImages.length === 0) {
      showMainImage(); // Hiển thị hình ảnh chính nếu không có ảnh mô tả
      prevThumbnailButton.style.display = 'none';
      nextThumbnailButton.style.display = 'none';
      return; // Không cần thực hiện tiếp
    }
    const endIndex = Math.min(thumbnailImages.length - 1, startIndex + 2);
    for (let i = startIndex; i <= endIndex; i++) {
      const thumbnail = document.createElement('img');
      thumbnail.className = 'thumbnail';
      thumbnail.src = '../../' + thumbnailImages[i];
      thumbnail.addEventListener('click', () => {
        showThumbnailImage(i);
      });
      thumbnailImagesContainer.appendChild(thumbnail);
    }
    if (thumbnailImages.length <= 3) {
      prevThumbnailButton.style.display = 'none';
      nextThumbnailButton.style.display = 'none';
    } else {
      prevThumbnailButton.style.display = 'block';
      nextThumbnailButton.style.display = 'block';
    }
  }

  updateThumbnailImages();

  prevThumbnailButton.addEventListener('click', () => {
    startIndex--;
    if (startIndex < 0) {
      startIndex = 0;
    }
    updateThumbnailImages();
  });

  nextThumbnailButton.addEventListener('click', () => {
    startIndex++;
    if (startIndex > thumbnailImages.length - 3) {
      startIndex = thumbnailImages.length - 3;
    }
    updateThumbnailImages();
  });
</script>
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