@extends('admin.layouts.app')
@section('title', 'Admin Profile')
@section('page-title', 'My Profile')

@section('content')
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body py-4">
                    <div class="mx-auto mb-3"
                        style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;">
                        <span
                            class="text-white fs-2 fw-bold">{{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}</span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ auth('admin')->user()->name }}</h5>
                    <div class="text-muted small">{{ auth('admin')->user()->email }}</div>
                    <span class="badge bg-primary-subtle text-primary mt-2">Admin</span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header fw-semibold"><i class="bi bi-person-circle me-2"></i>Update Profile</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.profile.update') }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Full Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', auth('admin')->user()->name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Email Address</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', auth('admin')->user()->email) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Mobile Number</label>
                                <input type="text" name="mobile" class="form-control"
                                    value="{{ old('mobile', auth('admin')->user()->mobile) }}" required>
                            </div>
                            <div class="col-12 border-top pt-3 mt-2">
                                <h6 class="fw-semibold text-muted small">Change Password (optional)</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Current Password</label>
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">New Password</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Save
                                    Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection