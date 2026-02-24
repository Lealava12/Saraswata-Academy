@extends('admin.layouts.app')
@section('title', 'Edit Study Material')
@section('page-title', 'Edit Study Material')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-bag-fill me-2"></i>Edit Material</span>
        <a href="{{ route('admin.study-materials.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.study-materials.update', $material->id) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-medium">Material Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $material->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $material->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Status</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ $material->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$material->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Update</button>
            <a href="{{ route('admin.study-materials.index') }}" class="btn btn-light ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection
