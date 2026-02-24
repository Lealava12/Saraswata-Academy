@extends('admin.layouts.app')
@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><span class="badge bg-secondary">{{ $student->student_id }}</span> {{ $student->name }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <!-- Student Info -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold"><i class="bi bi-person me-2"></i>Student Info</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Name</dt><dd class="col-sm-8 fw-medium">{{ $student->name }}</dd>
                    <dt class="col-sm-4 text-muted">Mobile</dt><dd class="col-sm-8">{{ $student->mobile }}</dd>
                    <dt class="col-sm-4 text-muted">Email</dt><dd class="col-sm-8">{{ $student->email ?: '-' }}</dd>
                    <dt class="col-sm-4 text-muted">DOB</dt><dd class="col-sm-8">{{ $student->dob ?: '-' }}</dd>
                    <dt class="col-sm-4 text-muted">Class</dt><dd class="col-sm-8">{{ $student->classInfo->name ?? '-' }}</dd>
                    <dt class="col-sm-4 text-muted">Board</dt><dd class="col-sm-8">{{ $student->board->name ?? '-' }}</dd>
                    <dt class="col-sm-4 text-muted">Roll No</dt><dd class="col-sm-8">{{ $student->roll_no }}</dd>
                    <dt class="col-sm-4 text-muted">Joining Date</dt><dd class="col-sm-8">{{ $student->joining_date }}</dd>
                    <dt class="col-sm-4 text-muted">School</dt><dd class="col-sm-8">{{ $student->school_name ?: '-' }}</dd>
                    <dt class="col-sm-4 text-muted">Address</dt><dd class="col-sm-8">{{ $student->address ?: '-' }}</dd>
                    <dt class="col-sm-4 text-muted">Status</dt>
                    <dd class="col-sm-8"><span class="badge {{ $student->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $student->is_active ? 'Active' : 'Inactive' }}</span></dd>
                </dl>
            </div>
        </div>
    </div>
    <!-- Parent Info -->
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-semibold"><i class="bi bi-people me-2"></i>Parent / Guardian</div>
            <div class="card-body">
                @if($student->parent)
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Father's Name</dt><dd class="col-sm-7">{{ $student->parent->father_name ?: '-' }}</dd>
                    <dt class="col-sm-5 text-muted">Father's Mobile</dt><dd class="col-sm-7">{{ $student->parent->father_mobile ?: '-' }}</dd>
                    <dt class="col-sm-5 text-muted">Mother's Name</dt><dd class="col-sm-7">{{ $student->parent->mother_name ?: '-' }}</dd>
                    <dt class="col-sm-5 text-muted">Mother's Mobile</dt><dd class="col-sm-7">{{ $student->parent->mother_mobile ?: '-' }}</dd>
                </dl>
                @else
                <p class="text-muted">No parent info recorded.</p>
                @endif
            </div>
        </div>
    </div>
    <!-- Fee History -->
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold d-flex justify-content-between">
                <span><i class="bi bi-cash-coin me-2"></i>Fee History</span>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.students.fee-history', $student->id) }}" class="btn btn-sm btn-outline-primary">Full History</a>
                    <a href="{{ route('admin.fees.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-outline-success">Add Entry</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Receipt No</th><th>Amount</th><th>Payment Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($student->fees as $fee)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $fee->receipt_no }}</span></td>
                                <td>₹{{ number_format($fee->amount) }}</td>
                                <td>{{ $fee->payment_date }}</td>
                                <td><span class="badge rounded-pill px-3 {{ $fee->status === 'Paid' ? 'badge-paid' : ($fee->status === 'Due' ? 'badge-due' : 'badge-overdue') }}">{{ $fee->status }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No fee records.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
