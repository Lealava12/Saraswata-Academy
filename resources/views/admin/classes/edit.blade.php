@extends('admin.layouts.app')
@section('title', 'Edit Class')
@section('page-title', 'Edit Class')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-layers-fill me-2"></i>Edit Class</span>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.classes.update', $class->id) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium">Class Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $class->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Board <span class="text-danger">*</span></label>
                    <select name="board_id" class="form-select" required>
                        @foreach($boards as $b)
                        <option value="{{ $b->id }}" {{ $class->board_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Monthly Fee (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="monthly_fee" class="form-control" value="{{ old('monthly_fee', $class->monthly_fee) }}" min="0" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $class->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$class->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Update</button>
                    <a href="{{ route('admin.classes.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
