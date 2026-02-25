@extends('admin.layouts.app')
@section('title', 'Edit Staff')
@section('page-title', 'Edit Non-Teaching Staff')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-pencil-square me-2"></i>Edit Staff Member</span>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.staff.update', $staff->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $staff->name) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium">Role / Designation <span class="text-danger">*</span></label>
                    <input type="text" name="role" class="form-control"
                           value="{{ old('role', $staff->role) }}"
                           placeholder="e.g. Peon, Guard, Clerk" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium">Mobile <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control"
                           value="{{ old('mobile', $staff->mobile) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium">Monthly Salary (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="monthly_salary" class="form-control"
                           min="0" step="0.01"
                           value="{{ old('monthly_salary', $staff->monthly_salary) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control"
                           value="{{ old('joining_date', optional($staff->joining_date)->format('Y-m-d')) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', (string)$staff->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', (string)$staff->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4">
                        <i class="bi bi-check-lg me-2"></i>Update
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection