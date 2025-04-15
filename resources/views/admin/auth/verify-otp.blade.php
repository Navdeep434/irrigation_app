@extends('admin.layouts.guest-layout')

@section('page-title', 'Verify OTP | Smart Irrigation')

@section('content')
<main class="auth-wrapper">
    <div class="auth-card shadow mx-auto" style="max-width: 400px;">
        <h4 class="text-center mb-4">Enter OTP</h4>

        <form id="otpForm" method="POST">
            @csrf
            <input type="hidden" name="email" id="email" value="{{ request()->query('email') }}">
            <input type="hidden" name="role" id="role" value="{{ request()->query('role') }}">

            <div class="mb-3">
                <label for="otp" class="form-label">OTP</label>
                <input type="text" class="form-control" name="otp" id="otp" required placeholder="Enter OTP">
            </div>

            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>

        <div class="alert mt-3 d-none" id="otpResult"></div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('#otpForm').submit(function (e) {
            e.preventDefault();

            const form = $(this);
            const formData = form.serialize();
            const resultDiv = $('#otpResult');
            $('#loader').removeClass('d-none');
            // Show a loading message or reset previous messages
            resultDiv.removeClass('d-none alert-danger alert-success');
            resultDiv.text('Verifying...').addClass('alert-info');

            $.ajax({
                url: "{{ route('admin.verifyOtp.post') }}",
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    $('#loader').removeClass('d-none');
                    resultDiv.removeClass('alert-info').addClass(data.status === 'success' ? 'alert-success' : 'alert-danger');
                    resultDiv.text(data.message);

                    // Redirect after successful OTP verification
                    if (data.status === 'success') {
                        window.location.href = '/admin/login';
                    }
                },
                error: function () {
                    $('#loader').removeClass('d-none');
                    resultDiv.removeClass('alert-info').addClass('alert-danger');
                    resultDiv.text('Something went wrong. Please try again.');
                }
            });
        });
    });
</script>

@endsection
