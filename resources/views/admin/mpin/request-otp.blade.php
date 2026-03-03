@extends('admin.layouts.app')

@section('title', 'Manage MPIN - Request OTP')
@section('page-title', 'Manage MPIN')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white text-center py-4 border-bottom-0">
                <div class="mb-3">
                    <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
                </div>
                <h4 class="fw-bold mb-1">Manage MPIN</h4>
                <p class="text-muted small mb-0">For security reasons, please verify your identity to view or edit the MPIN.</p>
            </div>
            <div class="card-body p-4 pt-2">
                <form action="{{ route('admin.manage-mpin.send-otp') }}" method="POST">
                    @csrf
                    <div class="alert alert-info border-0 d-flex align-items-center mb-4">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <span class="small">An OTP will be sent to your registered admin email address ({{ Auth::guard('admin')->user()->email }}).</span>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">
                        <i class="bi bi-envelope-paper me-2"></i>Send Security OTP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
