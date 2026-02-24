@extends('admin.layouts.app')
@section('title', 'Edit Admin')
@section('page-title', 'Edit Admin Account')

@section('content')
<div class="card" style="max-width:500px">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-shield-lock-fill me-2"></i>Edit Admin</span>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Mobile Number <span class="text-danger">*</span></label>
                <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $admin->mobile) }}" required>
                @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 border-top pt-3">
                <h6 class="fw-semibold text-muted small">Change Password (optional)</h6>
                <label class="form-label fw-medium">New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Status</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ $admin->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$admin->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Update Admin</button>
            <a href="{{ route('admin.admins.index') }}" class="btn btn-light ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection
