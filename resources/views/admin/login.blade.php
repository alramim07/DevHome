<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Dev Home</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="" name="keywords">
  <meta content="" name="description">

  <!-- Favicon -->
  <link href="{{ asset('admin/img/favicon.ico') }}" rel="icon">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap"
    rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Libraries Stylesheet -->
  <link href="{{ asset('admin/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
  <link href="{{ asset('admin/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

  <!-- Customized Bootstrap Stylesheet -->
  <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
  <!-- Template Stylesheet -->
  <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
</head>

<body>
  <div class="d-flex justify-content-center align-items-center min-vh-100 p-3">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
      <div class="bg-secondary rounded p-4 shadow">
        <h6 class="mb-4">Login To Dev Home</h6>
        <form action="{{ route('login.post') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email address</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control bg-dark text-white border-0 @error('email') is-invalid @enderror"
              id="exampleInputEmail1" aria-describedby="emailHelp" required autofocus>
            @error('email')
              <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <div class="input-group">
              <input type="password" name="password" class="form-control bg-dark text-white border-0 @error('password') is-invalid @enderror" id="exampleInputPassword1" required>
              <button class="btn btn-dark border-0 toggle-password-btn" type="button" id="togglePasswordBtn" title="Show/Hide Password" style="cursor: pointer; color: #6c757d;">
                <i class="fa fa-eye" id="togglePasswordIcon"></i>
              </button>
            </div>
            @error('password')
              <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3 form-check d-flex align-items-center">
            <input type="checkbox" name="remember" class="form-check-input mt-0" id="rememberMe">
            <label class="form-check-label ms-2 text-muted" for="rememberMe" style="cursor: pointer; user-select: none;">Remember me</label>
          </div>
          <button type="submit" class="btn btn-primary w-100">Sign in</button>
        </form>
      </div>
    </div>
  </div>


  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset("admin/lib/chart/chart.min.js") }}"></script>
  <script src="{{ asset('admin/lib/easing/easing.min.js') }}"></script>
  <script src="{{ asset('admin/lib/waypoints/waypoints.min.js') }}"></script>
  <script src="{{ asset('admin/lib/owlcarousel/owl.carousel.min.js') }}"></script>
  <script src="{{ asset('admin/lib/tempusdominus/js/moment.min.js') }}"></script>
  <script src="{{ asset('admin/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
  <script src="{{ asset('admin/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

  <!-- Template Javascript -->
  <script src="{{ asset('admin/js/main.js') }}"></script>
  <script>
    $(document).ready(function() {
      $('#togglePasswordBtn').on('click', function(e) {
        e.preventDefault();
        const input = $('#exampleInputPassword1');
        const icon = $('#togglePasswordIcon');
        if (input.attr('type') === 'password') {
          input.attr('type', 'text');
          icon.removeClass('fa-eye').addClass('fa-eye-slash text-primary');
        } else {
          input.attr('type', 'password');
          icon.removeClass('fa-eye-slash text-primary').addClass('fa-eye');
        }
      });
    });
  </script>
</body>

</html>