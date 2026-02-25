@extends('admin.layouts.app')
@section('title', 'Material Assignments')
@section('page-title', 'Material Assignments')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-person-check me-2"></i>
            Assignments: <strong>{{ $material->name }}</strong>
        </span>
        <div>
            <a href="{{ route('admin.study-materials.assign', ['material' => $material->id]) }}"
               class="btn btn-sm btn-success me-2">
                <i class="bi bi-plus-circle me-1"></i>New Assignment
            </a>
            <a href="{{ route('admin.study-materials.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="card-body">
        

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                   <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Issue Date</th>
                    <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $index => $assignment)
                       <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><span class="badge bg-secondary">{{ $assignment->student->student_id }}</span></td>
                        <td>{{ $assignment->student->name }}</td>
                        <td>{{ $assignment->student->classInfo->name ?? '-' }}</td>
                       <td>
   
                        <td>
                            <span class="badge {{ $assignment->status == 'Issued' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $assignment->status }}
                            </span>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No assignments found for this material.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Return Modal -->
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="returnForm">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Mark Material as Returned</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <input type="hidden" name="status" value="Returned">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Return</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function markReturned(assignmentId) {
    const form = document.getElementById('returnForm');
    form.action = `{{ url('admin/study-materials/assignment') }}/${assignmentId}/update-status`;
    new bootstrap.Modal(document.getElementById('returnModal')).show();
}
</script>
@endpush
@endsection