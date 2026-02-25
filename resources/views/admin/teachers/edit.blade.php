@extends('admin.layouts.app')
@section('title', 'Edit Teacher')
@section('page-title', 'Edit Teacher')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-pencil me-2"></i>Edit Teacher</span>
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.teachers.update', $teacher->id) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mobile <span class="text-danger">*</span></label>
                    <input type="number" name="mobile" class="form-control" value="{{ old('mobile', $teacher->mobile) }}" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);"  required>
                   
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $teacher->joining_date) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $teacher->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$teacher->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address', $teacher->address) }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Subjects</label>
                    <div class="row g-2">
                        @foreach($subjects as $s)
                        <div class="col-md-3 col-sm-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="subjects[]" id="subj{{ $s->id }}" value="{{ $s->id }}"
                                    {{ in_array($s->id, $assignedSubjects) ? 'checked' : '' }}>
                                <label class="form-check-label" for="subj{{ $s->id }}">{{ $s->name }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Update</button>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
