@extends('admin.layouts.app')
@section('title', 'Edit Exam')
@section('page-title', 'Edit Exam')

@section('content')
<div class="card" style="max-width:600px">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-journal-text me-2"></i>Edit Exam</span>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.exams.update', $exam->id) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select" required>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $exam->class_id == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->board ? ' ('. $c->board->name .')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Subject <span class="text-danger">*</span></label>
                    <select name="subject_id" class="form-select" required>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ $exam->subject_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Exam Date <span class="text-danger">*</span></label>
                    <input type="date" name="exam_date" class="form-control" value="{{ old('exam_date', $exam->exam_date) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Full Marks <span class="text-danger">*</span></label>
                    <input type="number" name="full_marks" class="form-control" value="{{ old('full_marks', $exam->full_marks) }}" min="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $exam->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$exam->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Update</button>
                    <a href="{{ route('admin.exams.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
