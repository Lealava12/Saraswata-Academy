@extends('admin.layouts.app')
@section('title', 'Edit Expenditure')
@section('page-title', 'Edit Expenditure')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-receipt me-2"></i>Edit Expenditure</span>
        <a href="{{ route('admin.expenditures.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.expenditures.update', $expenditure->id) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $expenditure->title) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ old('amount', $expenditure->amount) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                    <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expenditure->expense_date) }}" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label fw-medium">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $expenditure->description) }}</textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $expenditure->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$expenditure->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Update</button>
                    <a href="{{ route('admin.expenditures.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
