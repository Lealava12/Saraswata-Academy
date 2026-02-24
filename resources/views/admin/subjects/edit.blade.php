@extends('admin.layouts.app')
@section('title', 'Edit Subject')
@section('page-title', 'Edit Subject')

@section('content')
<div class="card" style="max-width:500px">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-book-fill me-2"></i>Edit Subject</span>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.subjects.update', $subject->id) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-medium">Subject Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $subject->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Status</label>
                <select name="is_active" class="form-select">
                    <option value="1" {{ $subject->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$subject->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Update</button>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-light ms-2">Cancel</a>
        </form>
    </div>
</div>
@endsection
