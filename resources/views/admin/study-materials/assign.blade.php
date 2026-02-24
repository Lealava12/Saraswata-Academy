@extends('admin.layouts.app')
@section('title', 'Assign Material')
@section('page-title', 'Assign Material to Students')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-check me-2"></i>Assign: <strong>{{ $material->name }}</strong></span>
        <a href="{{ route('admin.study-materials.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.study-materials.do-assign', $material->id) }}">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Issue Date <span class="text-danger">*</span></label>
                    <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="status" class="form-select">
                        <option value="Issued">Issued</option>
                        <option value="Returned">Returned</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                            <th>Student ID</th><th>Name</th><th>Class</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $s)
                        <tr>
                            <td>
                                <input type="checkbox" name="students[]" value="{{ $s->id }}" class="form-check-input student-check"
                                    {{ $assigned->contains($s->id) ? 'checked disabled' : '' }}>
                            </td>
                            <td><span class="badge bg-secondary">{{ $s->student_id }}</span></td>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->classInfo->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-person-check me-2"></i>Assign to Selected</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.student-check:not([disabled])').forEach(cb => { cb.checked = this.checked; });
});
</script>
@endpush
