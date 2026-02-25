@extends('admin.layouts.app')
@section('title', 'Add Study Material')
@section('page-title', 'Add Study Material')

@section('content')
<div class="card" style="max-width:650px">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-bag-fill me-2"></i>Add Material</span>
        <a href="{{ route('admin.study-materials.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.study-materials.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-medium">Material Name <span class="text-danger">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                          rows="4">{{ old('description') }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-accent px-4">
                <i class="bi bi-check-lg me-1"></i>Save
            </button>
            <a href="{{ route('admin.study-materials.index') }}" class="btn btn-light ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection