@extends('admin.layouts.app')
@section('title', 'Assign Material')
@section('page-title', 'Assign Material to Student')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-person-check me-2"></i>
            Assign: <strong>{{ $material->name }}</strong>
        </span>
        <div>
            <a href="{{ route('admin.study-materials.assignments', ['material' => $material->id]) }}"
               class="btn btn-sm btn-outline-info me-2">
                <i class="bi bi-eye me-1"></i>View Assignments
            </a>
            <a href="{{ route('admin.study-materials.index') }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.study-materials.do-assign', ['material' => $material->id]) }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Select Student <span class="text-danger">*</span></label>

                   <select name="student_id"
                    class="form-select student-select @error('student_id') is-invalid @enderror"
                    required>
                <option value="">-- Select Student --</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                        {{ $student->student_id }} - {{ $student->name }} ({{ $student->classInfo->name ?? '-' }})
                    </option>
                @endforeach
            </select>

                    @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Issue Date <span class="text-danger">*</span></label>
                    <input type="date" name="issue_date"
                           class="form-control @error('issue_date') is-invalid @enderror"
                           value="{{ old('issue_date', date('Y-m-d')) }}" required>
                    @error('issue_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                   <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="Issued" {{ old('status') == 'Issued' ? 'selected' : '' }}>Issued</option>
                    <option value="Not Issued" {{ old('status') == 'Not Issued' ? 'selected' : '' }}>Not Issued</option>
                </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-accent px-4">
                    <i class="bi bi-person-check me-1"></i>Assign Material
                </button>
                <a href="{{ route('admin.study-materials.index') }}" class="btn btn-light ms-2">Cancel</a>
            </div>
        </form>

    </div>
</div>
 
@endsection
@push('styles')
<style>
    .select2-container .select2-selection--single{
        height: 38px;
        padding: 4px 10px;
        border: 1px solid #ced4da;
        border-radius: .375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 38px;
    }
</style>
@endpush

@push('scripts')
 <script>
$(function () {
    $('.student-select').select2({
        placeholder: "-- Select Student --",
        allowClear: true,
        width: '100%'
    });
});
</script>
@endpush