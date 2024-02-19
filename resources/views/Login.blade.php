<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>FoodShare</title>
  <meta name="description" content="" />
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />
  <!-- Icons. Uncomment required icon fonts -->
  <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- Page CSS -->
  <!-- Page -->
  <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />
  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>

  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  <script src="../assets/js/config.js"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Register -->
        <div class="card">
          <div class="card-body">
            <!-- demo AJAX Võ Văn Bình -->
            <!-- {{-- Toast --}} -->
            <!-- Logo -->
            <div class="app-brand justify-content-center">
              <a href="" class="app-brand-link gap-2">
                <img alt="logo" style="max-width:50px;" src="/assets/img/icons/brands/logo.png">
                <span class="app-brand-text demo text-body fw-bolder">FoodShare</span>
              </a>
            </div>
            <!-- /Logo -->
            <h4 class="mb-2">Welcome to FoodShare👋</h4>
            <p class="mb-4">Please sign-in to your account and start the adventure</p>
            <!-- form -->
            <form id="form_login" class="mb-3" action="index.html" method="POST">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your Email"
                  autofocus />
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="input-group input-group-merge">
                  <input type="password" id="password" class="form-control" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
              </div>
              <div class="mb-3">
                <button type="submit" class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
              </div>
            </form>
          </div>
        </div>
        <!-- /Register -->

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
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <script src="../assets/js/main.js"></script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
<script>
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
    $('#form_login').on('submit', function (e) {
      e.preventDefault();
      var formData = $(this).serialize();
      $.ajax({
        type: 'POST',
        url: '{{ route("login") }}',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'Accept': 'application/json'
        },
        data: formData,
        dataType: 'json',
        success: function (response) {
          if (response.errors) {
            showToast(response.errors, 'danger');
          }
          else if (response.admin) {
            window.location.href = '{{route("show_dashboard")}}';
          }
        },
        error: function (error) {
          // trường hợp lỗi ajax
          if (error.status === 422) {
            var errors = error.responseJSON.errors;
            console.log(errors);
          }
        }
      });
    });
  });
</script>

</html>