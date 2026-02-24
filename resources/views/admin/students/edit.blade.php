@extends('admin.layouts.app')
@section('title', 'Edit Student')
@section('page-title', 'Edit Student')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-pencil me-2"></i>Edit Student – <strong>{{ $student->student_id }}</strong></span>
        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.students.update', $student->id) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $student->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mobile <span class="text-danger">*</span></label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $student->mobile) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="{{ old('dob', $student->dob) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Board <span class="text-danger">*</span></label>
                    <select name="board_id" class="form-select" required>
                        @foreach($boards as $b)
                        <option value="{{ $b->id }}" {{ $student->board_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select" required>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $student->class_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $student->joining_date) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $student->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$student->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">New Password <small class="text-muted">(leave blank to keep)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-medium">School Name</label>
                    <input type="text" name="school_name" class="form-control" value="{{ old('school_name', $student->school_name) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">New Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="col-12"><hr class="my-1"><h6 class="text-muted fw-semibold">Parent Details</h6></div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Father's Name</label>
                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $student->parent->father_name ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Father's Mobile</label>
                    <input type="text" name="father_mobile" class="form-control" value="{{ old('father_mobile', $student->parent->father_mobile ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mother's Name</label>
                    <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $student->parent->mother_name ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mother's Mobile</label>
                    <input type="text" name="mother_mobile" class="form-control" value="{{ old('mother_mobile', $student->parent->mother_mobile ?? '') }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-medium">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $student->parent->address ?? '') }}</textarea>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Update Student</button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
