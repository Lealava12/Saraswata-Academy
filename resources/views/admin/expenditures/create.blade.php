@extends('admin.layouts.app')
@section('title', 'Add Expenditure')
@section('page-title', 'Add Expenditure')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2"></i>Add Expenditure</span>
        <a href="{{ route('admin.expenditures.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.expenditures.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Electricity Bill" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ old('amount') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                    <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label fw-medium">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Optional details…">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Save</button>
                    <a href="{{ route('admin.expenditures.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
