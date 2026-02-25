<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP – Saraswata Academy</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #1a1f2e 0%, #2d3461 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
}
.login-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.3);
    max-width: 420px;
    width: 100%;
}
.login-logo {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg,#6366f1,#8b5cf6);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}
.btn-primary {
    background: #6366f1;
    border: none;
}
.btn-primary:hover {
    background: #4f46e5;
}
.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 .2rem rgba(99,102,241,.25);
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
<i class="bi bi-shield-lock-fill text-white fs-2"></i>
</div>
<h4 class="fw-bold">OTP Verification</h4>
<p class="text-muted small">
Enter the 6-digit OTP sent to your registered email
</p>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('admin.verify.otp') }}">
@csrf

<div class="mb-3">
<label class="form-label fw-medium">Enter OTP</label>

<div class="input-group">
<span class="input-group-text">
<i class="bi bi-key"></i>
</span>

<input type="text"
class="form-control form-control-lg text-center @error('otp') is-invalid @enderror"
name="otp"
placeholder="••••••"
maxlength="6"
pattern="[0-9]{6}"
required>
</div>

@error('otp')
<div class="invalid-feedback d-block">
{{ $message }}
</div>
@enderror

</div>

<div class="d-grid mt-4">
<button type="submit" class="btn btn-primary py-2 fw-semibold">
<i class="bi bi-check-circle me-2"></i>
Verify OTP
</button>
</div>

</form>

<hr class="my-4">

<div class="text-center">
<a href="{{ route('admin.login') }}" class="text-decoration-none small">
← Back to Login
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