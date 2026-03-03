@extends('admin.layouts.app')

@section('title', 'Verify OTP - Manage MPIN')
@section('page-title', 'Verify Security OTP')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white text-center py-4 border-bottom-0">
                <div class="mb-3">
                    <i class="bi bi-envelope-check text-success" style="font-size: 3rem;"></i>
                </div>
                <h4 class="fw-bold mb-1">Verify OTP</h4>
                <p class="text-muted small mb-0">Enter the 6-digit code sent to your email.</p>
            </div>
            <div class="card-body p-4 pt-2">
                <form action="{{ route('admin.manage-mpin.verify-otp') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-medium text-muted small text-uppercase">Security OTP</label>
                        <input type="text" name="otp" class="form-control form-control-lg text-center letter-spacing-3 fs-3 @error('otp') is-invalid @enderror" 
                            maxlength="6" placeholder="------" required autofocus autocomplete="off">
                        @error('otp')
                            <div class="invalid-feedback text-center mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 py-2 fw-medium">
                        <i class="bi bi-check-circle me-2"></i>Verify & Continue
                    </button>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted small mb-0">Didn't receive the code?</p>
                        <form action="{{ route('admin.manage-mpin.send-otp') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link pe-0 text-decoration-none small fw-medium text-primary">Resend OTP</button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .letter-spacing-3 {
        letter-spacing: 0.5em;
    }
</style>
@endpush
