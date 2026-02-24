@extends('admin.layouts.app')
@section('title', 'Add Subject')
@section('page-title', 'Add Subject')

@section('content')
<div class="card" style="max-width:500px">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-book-fill me-2"></i>Add Subject</span>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.subjects.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Subject Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Mathematics" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Status</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Save</button>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-light ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection
