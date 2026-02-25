<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP – Student Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .login-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .3);
        max-width: 420px;
        width: 100%;
    }

    .login-logo {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .btn-primary {
        background: #3b82f6;
        border: none;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 .2rem rgba(59, 130, 246, .25);
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <div class="card login-card p-4">
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <div class="login-logo">
                                <i class="bi bi-shield-check text-white fs-2"></i>
                            </div>
                            <h4 class="fw-bold">OTP Verification</h4>
                            <p class="text-muted small">Student Portal · Saraswata Academy</p>
                        </div>

                        @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        <form method="POST" action="{{ route('student.verify.otp') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-medium">Enter 6-digit OTP</label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-key"></i>
                                    </span>

                                    <input type="text" name="otp"
                                        class="form-control text-center @error('otp') is-invalid @enderror"
                                        placeholder="••••••" maxlength="6" pattern="[0-9]{6}" required>
                                </div>

                                @error('otp')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary py-2 fw-semibold">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Verify OTP
                                </button>
                            </div>

                        </form>

                        <div class="text-center mt-4">
                            <a href="{{ route('student.login') }}" class="text-decoration-none small text-muted">
                                ← Back to Student Login
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>