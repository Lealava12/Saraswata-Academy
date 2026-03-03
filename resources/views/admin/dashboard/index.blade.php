@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Stat Cards Row 1 -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card text-white" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
            <div class="text-white-50 small mb-1">Total Students</div>
            <div class="fs-2 fw-bold">{{ $totalStudents }}</div>
            <i class="bi bi-people-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card text-white" style="background:linear-gradient(135deg,#10b981,#059669)">
            <div class="text-white-50 small mb-1">Total Teachers</div>
            <div class="fs-2 fw-bold">{{ $totalTeachers }}</div>
            <i class="bi bi-person-badge-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="text-white-50 small mb-1">Total Staff</div>
            <div class="fs-2 fw-bold">{{ $totalStaff }}</div>
            <i class="bi bi-person-gear stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card text-white" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
            <div class="text-white-50 small mb-1">Overdue Fees</div>
            <div class="fs-2 fw-bold">{{ $overdueCount }}</div>
            <i class="bi bi-exclamation-triangle-fill stat-icon"></i>
        </div>
    </div>
</div>

<!-- Finance Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">This Month Fee Collection</div>
                <div class="fs-3 fw-bold text-success mt-2">₹{{ number_format($feeCollected, 2) }}</div>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <span><span class="badge badge-due">Due ₹{{ number_format($feeDue) }}</span></span>
                    <span><span class="badge badge-overdue">Overdue ₹{{ number_format($feeOverdue) }}</span></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">This Month Salary Paid</div>
                <div class="fs-3 fw-bold text-warning mt-2">₹{{ number_format($teacherPaid + $staffPaid, 2) }}</div>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <span class="text-muted small">Teachers ₹{{ number_format($teacherPaid) }}</span>
                    <span class="text-muted small">Staff ₹{{ number_format($staffPaid) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="text-muted small">This Month Expenditure</div>
                <div class="fs-3 fw-bold text-danger mt-2">₹{{ number_format($totalExpenditure, 2) }}</div>
                <a href="{{ route('admin.reports.financial') }}" class="btn btn-sm btn-outline-secondary mt-3">View Financial Report</a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Students + Overdue -->
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-people-fill me-2 text-primary"></i>Recent Students</span>
                <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Student ID</th><th>Name</th><th>Class</th><th>Board</th><th>Roll No</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentStudents as $s)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $s->student_id }}</span></td>
                                <td>{{ $s->name }}</td>
                                <td>{{ $s->classInfo->name ?? '-' }}</td>
                                <td>{{ $s->board->name ?? '-' }}</td>
                                <td>{{ $s->roll_no }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No students found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        @if($latePaymentAlerts->count() > 0)
        <div class="card border-danger mb-3 shadow-sm">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i>Critical Late Payments</h6>
            </div>
            <div class="card-body py-2">
                <div class="small text-muted mb-2">Following students have pending fees for over 10 days:</div>
                <ul class="list-group list-group-flush">
                    @foreach($latePaymentAlerts as $s)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <div>
                            <div class="fw-bold">{{ $s->name }}</div>
                            <small class="text-muted">{{ $s->classInfo->name ?? '-' }} | ₹{{ number_format($s->getBalanceDue()) }}</small>
                        </div>
                        <a href="{{ route('admin.fees.create', ['student_id' => $s->id]) }}" class="btn btn-xs btn-outline-danger">Collect</a>
                    </li>
                    @endforeach
                </ul>
                <div class="text-center mt-2">
                    <a href="{{ route('admin.fees.pending') }}" class="btn btn-link btn-sm text-danger p-0">View All Pending</a>
                </div>
            </div>
        </div>
        @endif

        <!-- <div class="card border-danger">
            <div class="card-header bg-danger text-white d-flex justify-content-between">
                <span><i class="bi bi-exclamation-triangle-fill me-2"></i>Overdue Fees</span>
                <a href="{{ route('admin.fees.index') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Student</th><th>Due Date</th><th>Amount</th></tr>
                        </thead>
                        <tbody>
                            @forelse($overdueStudents as $fee)
                            <tr>
                                <td>{{ $fee->student->name ?? '-' }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($fee->due_date)->format('d M, Y') }}<br>
                                    <span class="text-danger small fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Late Payment!</span>
                                </td>
                                <td class="text-danger fw-semibold">₹{{ number_format($fee->amount) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">No overdue fees 🎉</td></tr>
                            @endforelse 
                        </tbody>
                    </table>
                </div>
            </div>
        </div> -->
    </div>
</div>
@endsection
