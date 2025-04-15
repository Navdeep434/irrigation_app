@extends('web.layouts.guest-layout')

@section('page-title', 'Signup | Smart Irrigation')

@section('content')
    <style>
        .file-input-compact {
            padding: 0.25rem 0.5rem;
        }
        
        /* For even more compact file inputs */
        .file-input-very-compact {
            padding: 0.125rem 0.25rem;
        }
    </style>
    <main class="auth-wrapper">
        <div class="auth-card shadow">
            <h2 class="text-center">Create an account</h2>

            <div class="social-buttons">
                <a href="#" class="social-button">
                    <img src="/images/icons/google-icon.png" alt="Google" style="width:20px; height:20px; vertical-align:middle; margin-right:8px;">
                    Google
                </a>
                <a href="#" class="social-button">
                    <img src="/images/icons/facebook-icon.png" alt="Facebook" style="width:20px; height:20px; vertical-align:middle; margin-right:8px;">
                    Facebook
                </a>
            </div>

            <div class="divider">
                <span>Or</span>
            </div>

            <form id="signupForm">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-row">
                    <div>
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Contact Number</label>
                    <div class="input-group">
                        <span class="input-group-text">+</span>
                        <input type="text" name="country_code" class="form-control" placeholder="Code" style="max-width: 80px;" required>
                        <input type="text" name="contact_number" class="form-control" placeholder="Phone number" required>
                    </div>
                    <div class="form-text">Example: +1 for US/Canada, +44 for UK, +91 for India</div>
                </div>

                <div class="mb-3">
                    <label for="profile_image" class="form-label">Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image" class="form-control file-input-compact">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select" required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Create account</button>
            </form>

            <div class="text-center mt-3">
                Already have an account? <a href="/login" class="text-info">Log in</a>
            </div>
        </div>
    </main>

    <!-- Toast message for success/error -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="toastMessage" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            const toastEl = document.getElementById('toastMessage');
            const toastBody = document.getElementById('toastBody');
            const toast = new bootstrap.Toast(toastEl);

            $('#signupForm').on('submit', function (e) {
                e.preventDefault();
                $('#loader').removeClass('d-none');

                $.ajax({
                    url: '{{ route("user.signup.post") }}',
                    type: 'POST',
                    data: $('#signupForm').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('#loader').removeClass('d-none');

                        toastBody.innerText = response.message;
                        toastEl.classList.remove('text-bg-danger');
                        toastEl.classList.add('text-bg-success');
                        toast.show();

                        // Redirect to OTP page after success
                        setTimeout(() => {
                            const email = $('input[name="email"]').val();
                            window.location.href = '/verify-otp?email=' + encodeURIComponent(email);
                        }, 500);
                    },
                    error: function (xhr) {
                        $('#loader').removeClass('d-none');

                        let errorMsg = 'Signup failed. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMsg = Object.values(errors).map(err => err[0]).join('\n');
                        }

                        toastBody.innerText = errorMsg;
                        toastEl.classList.remove('text-bg-success');
                        toastEl.classList.add('text-bg-danger');
                        toast.show();
                    }
                });
            });
        });
    </script>
    
@endsection
