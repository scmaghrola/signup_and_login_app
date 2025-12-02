<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up with OTP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .signup-container {
            max-width: 420px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Toast container - bottom center */
        #toastContainer {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1055;
        }

        .toast {
            min-width: 280px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="signup-container">
        <h3 class="text-center mb-4">Sign Up</h3>

        <form method="POST" id="signupForm">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    placeholder="Enter your email address" required>

                <div class="d-grid gap-2 mt-2 w-50">
                    <button type="button" id="sendOtpBtn" class="btn btn-outline-secondary">Send OTP</button>
                </div>
            </div>

            <div class="mb-3" id="otpSection" style="display: none;">
                <label for="otp" class="form-label fw-semibold">Enter OTP</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP">
                    <button type="button" id="verifyOtpBtn" class="btn btn-success">Verify</button>
                </div>
                <small class="text-muted">Check your email for the OTP.</small>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>

            </div>

            <button type="submit" id="signupBtn" class="btn btn-primary w-100" disabled>Sign Up</button>
        </form>
    </div>

    <!-- Toast container -->
    <div id="toastContainer"></div>

    <script>
        function showToast(message, type = 'info') {
            const toastId = 'toast-' + Date.now();
            const bgClass = {
                success: 'bg-success text-white',
                error: 'bg-danger text-white',
                info: 'bg-primary text-white',
                warning: 'bg-warning text-dark'
            } [type] || 'bg-secondary text-white';

            const toastHTML = `
                <div id="${toastId}" class="toast align-items-center ${bgClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex justify-content-center">
                        <div class="toast-body">${message}</div>
                    </div>
                </div>`;

            $('#toastContainer').append(toastHTML);
            const toastEl = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();

            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }

        $(function() {
            let email = '';
            let verified = false;

            $('#sendOtpBtn').click(function() {
                email = $('#email').val();
                if (!email) return showToast('Please enter your email first.', 'warning');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                    url: '{{ route('sendOtp') }}',
                    data: {
                        email: email
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#otpSection').show();
                            $('#sendOtpBtn').prop('disabled', true).text('OTP Sent');
                            showToast('OTP sent to your email.', 'success');
                        } else {
                            showToast(res.message || 'Failed to send OTP.', 'error');
                        }
                    },
                    error: function() {
                        showToast('Error sending OTP.', 'error');
                    }
                });
            });

            $('#verifyOtpBtn').click(function() {
                const otp = $('#otp').val();
                if (!otp) return showToast('Please enter OTP.', 'warning');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                    url: '{{ route('verifyOtp') }}',
                    data: {
                        email: email,
                        otp: otp
                    },
                    success: function(res) {
                        if (res.success) {
                            showToast('OTP verified successfully!', 'success');
                            verified = true;
                            $('#signupBtn').prop('disabled', false);
                            $('#verifyOtpBtn').prop('disabled', true);
                            $('#otp').prop('disabled', true);
                        } else {
                            showToast(res.message, 'error');
                        }
                    },
                    error: function() {
                        showToast('Error verifying OTP.', 'error');
                    }
                });
            });

            $('#signupForm').submit(function(e) {
                e.preventDefault(); // always stop normal form submission

                if (!verified) {
                    showToast('Please verify your OTP before signing up.', 'warning');
                    return;
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'POST',
                    url: '{{ route('signup') }}',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            showToast(res.message, 'success');
                            setTimeout(() => {
                                window.location.href = '{{ route('home') }}';
                            }, 1500);
                        } else {
                            showToast(res.message || 'Signup failed.', 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast('Error during signup.', 'error');
                    }
                });
            });

        });
    </script>
</body>

</html>
