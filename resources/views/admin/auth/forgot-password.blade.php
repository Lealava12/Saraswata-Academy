<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password – Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter',sans-serif; background:linear-gradient(135deg,#1a1f2e,#2d3461); min-height:100vh; display:flex; align-items:center; }
        .card { border:none; border-radius:20px; box-shadow:0 20px 60px rgba(0,0,0,.3); max-width:420px; width:100%; }
        .btn-primary { background:#6366f1; border:none; }
        .btn-primary:hover { background:#4f46e5; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-lock-fill fs-1 text-primary"></i>
                        <h5 class="fw-bold mt-2">Forgot Password</h5>
                        <p class="text-muted small">Admin Portal · Saraswata Academy</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    @if(!session('otp_sent'))
                    <form method="POST" action="{{ route('admin.forgot.otp') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-medium">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="admin@saraswata.edu" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2">Send OTP</button>
                        </div>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.reset.password') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-medium">OTP</label>
                            <input type="text" name="otp" class="form-control" placeholder="6-digit OTP" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">New Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2">Reset Password</button>
                        </div>
                    </form>
                    @endif
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.login') }}" class="text-decoration-none small text-muted">← Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
