@extends('web.layouts.guest-layout')

@section('page-title', 'Login | Smart Irrigation')

@section('content')

  <main class="auth-wrapper">
    <div class="auth-card shadow">
      <h2 class="text-center">Sign In</h2>

      <!-- Error Message Container -->
      <div id="error-message" class="text-danger text-center d-none">
        Username or Password is Invalid.
      </div>

      <form id="loginForm" method="POST" action="{{ route('user.login.post') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="email@example.com" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <div class="forgot-link">
          <a href="">Forgot Your Password?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <div class="text-center mt-3">
        Don't have an account? <a href="/signup" class="text-info">Sign up</a>
      </div>
    </div>
  </main>

  <!-- Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <h5 class="modal-title text-success">Login Successful</h5>
        <p>You will be redirected to the dashboard.</p>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#loginForm').on('submit', function (e) {
        e.preventDefault();
  
        const email = $('#email').val();
        const password = $('#password').val();
        const token = $('meta[name="csrf-token"]').attr('content');
  
        // Show loader
        $('#loader').removeClass('d-none');
  
        $.ajax({
          url: '{{ route("user.login.post") }}',
          method: 'POST',
          data: {
            _token: token,
            email: email,
            password: password
          },
          success: function (response) {
            $('#loader').addClass('d-none');
            $('#error-message').addClass('d-none');
  
            // const modal = new bootstrap.Modal(document.getElementById('successModal'));
            // modal.show();
  
            setTimeout(() => {
              window.location.href = response.redirect_url;
            }, 1000);
          },
          error: function (xhr) {
            $('#loader').addClass('d-none');
  
            let errorMsg = 'Username or Password is Invalid...';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMsg = xhr.responseJSON.message;
            }
  
            $('#error-message').text(errorMsg).removeClass('d-none');
          }
        });
      });
    });
  </script>

@endsection
