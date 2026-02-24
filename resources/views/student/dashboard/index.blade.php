@extends('student.layouts.app')
@section('title', 'My Dashboard')

@section('content')
<h5 class="fw-bold mb-4"><i class="bi bi-grid-1x2-fill me-2 text-primary"></i>Welcome, {{ $student->name }}!</h5>

<!-- Student Info Banner -->
<div class="card mb-4" style="background:linear-gradient(135deg,#1e3a8a,#6366f1);color:#fff;">
    <div class="card-body d-flex align-items-center gap-4 flex-wrap">
        <div>
            <div class="fs-4 fw-bold">{{ $student->student_id }}</div>
            <div class="opacity-75 small">Student ID</div>
        </div>
        <div class="vr opacity-25"></div>
        <div>
            <div class="fw-semibold">{{ $student->classInfo->name ?? '-' }}</div>
            <div class="opacity-75 small">Class</div>
        </div>
        <div class="vr opacity-25"></div>
        <div>
            <div class="fw-semibold">{{ $student->board->name ?? '-' }}</div>
            <div class="opacity-75 small">Board</div>
        </div>
        <div class="vr opacity-25"></div>
        <div>
            <div class="fw-semibold">Roll No. {{ $student->roll_no }}</div>
            <div class="opacity-75 small">Roll Number</div>
        </div>
    </div>
</div>

<!-- Alert: Overdue -->
@if($overdueCount > 0)
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div>You have <strong>{{ $overdueCount }}</strong> overdue fee(s). <a href="{{ route('student.fees') }}" class="alert-link">View details</a></div>
</div>
@endif

<div class="row g-3">
    <!-- Today's Attendance -->
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-calendar-check fs-1 {{ $todayAttendance ? ($todayAttendance->status === 'Present' ? 'text-success' : 'text-danger') : 'text-muted' }}"></i>
                <div class="fw-semibold mt-2">Today's Attendance</div>
                @if($todayAttendance)
                    <span class="badge {{ $todayAttendance->status === 'Present' ? 'badge-present' : 'badge-absent' }} mt-1 px-3 py-1 rounded-pill">
                        {{ $todayAttendance->status }}
                    </span>
                @else
                    <div class="text-muted small mt-1">Not marked yet</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Next Fee Due -->
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-cash-coin fs-1 {{ $nextFee ? 'text-warning' : 'text-success' }}"></i>
                <div class="fw-semibold mt-2">Next Fee Due</div>
                @if($nextFee)
                    <div class="text-danger fw-bold mt-1">₹{{ number_format($nextFee->amount) }}</div>
                    <div class="text-muted small">Due: {{ $nextFee->due_date }}</div>
                @else
                    <div class="text-success small mt-1">All fees paid ✓</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Last Exam -->
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-journal-text fs-1 text-primary"></i>
                <div class="fw-semibold mt-2">Last Exam</div>
                @if($lastExam)
                    <div class="fw-bold mt-1 text-primary">{{ $lastExam->marks_obtained }} / {{ $lastExam->exam->full_marks }}</div>
                    <div class="text-muted small">{{ $lastExam->exam->subject->name ?? '-' }}</div>
                @else
                    <div class="text-muted small mt-1">No exam records</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Latest Material -->
    <div class="col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-bag-fill fs-1 text-info"></i>
                <div class="fw-semibold mt-2">Latest Material</div>
                @if($latestMaterial)
                    <div class="text-truncate fw-bold mt-1 text-info small">{{ $latestMaterial->material->name ?? '-' }}</div>
                    <div class="text-muted small">Issued: {{ $latestMaterial->issue_date }}</div>
                @else
                    <div class="text-muted small mt-1">No materials assigned</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Quick Access</h6>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('student.fees') }}" class="btn btn-outline-success btn-sm"><i class="bi bi-cash-coin me-1"></i>Fee History</a>
                    <a href="{{ route('student.attendance') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-calendar-check me-1"></i>Attendance</a>
                    <a href="{{ route('student.exams') }}" class="btn btn-outline-info btn-sm"><i class="bi bi-journal-text me-1"></i>Exam Results</a>
                    <a href="{{ route('student.materials') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-bag me-1"></i>My Materials</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
