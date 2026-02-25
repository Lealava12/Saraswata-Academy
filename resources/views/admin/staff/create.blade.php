@extends('admin.layouts.app')
@section('title', 'Add Staff')
@section('page-title', 'Add Non-Teaching Staff')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-plus-fill me-2"></i>Add Staff Member</span>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.staff.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Role / Designation <span class="text-danger">*</span></label>
                    <input type="text" name="role" class="form-control" value="{{ old('role') }}" placeholder="e.g. Peon, Guard, Clerk" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mobile <span class="text-danger">*</span></label>
                     <input type="number" 
                name="mobile" 
                class="form-control" 
                value="{{ old('mobile') }}" 
                oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);" 
                required>

                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Monthly Salary (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="monthly_salary" class="form-control" min="0" step="0.01" value="{{ old('monthly_salary') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Save</button>
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
