@extends('student.layouts.app')
@section('title', 'Exam Results')

@section('content')
<h5 class="fw-bold mb-4"><i class="bi bi-journal-text me-2 text-info"></i>Exam Results</h5>

<div class="card mb-4">
    <div class="card-body text-center py-4">
        <div class="fs-1 fw-bold {{ $avgPercentage >= 60 ? 'text-success' : 'text-warning' }}">{{ $avgPercentage }}%</div>
        <div class="text-muted">Average Score Across All Exams</div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-auto">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-6 col-md-auto">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-12 col-md-auto d-flex gap-1 mt-2 mt-md-0">
                <button class="btn btn-sm btn-outline-primary flex-grow-1 flex-md-grow-0">Filter</button>
                <a href="{{ route('student.exams') }}" class="btn btn-sm btn-light flex-grow-1 flex-md-grow-0">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Date</th><th>Subject</th><th>Full Marks</th><th>Obtained</th><th>%</th><th>Grade</th></tr>
            </thead>
            <tbody>
                @forelse($marks as $m)
                @php
                    $full = $m->exam->full_marks ?? 0;
                    $pct  = $full > 0 ? round(($m->marks_obtained / $full) * 100, 1) : 0;
                    $grade = $pct >= 90 ? 'A+' : ($pct >= 75 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 45 ? 'C' : 'F')));
                    $gradeColor = $pct >= 75 ? 'success' : ($pct >= 45 ? 'warning' : 'danger');
                @endphp
                <tr>
                    <td>{{ optional($m->exam)->exam_date ?? '-' }}</td>
                    <td>{{ optional(optional($m->exam)->subject)->name ?? '-' }}</td>
                    <td>{{ $full }}</td>
                    <td class="fw-semibold">{{ $m->marks_obtained }}</td>
                    <td>{{ $pct }}%</td>
                    <td><span class="badge bg-{{ $gradeColor }}-subtle text-{{ $gradeColor }} px-3">{{ $grade }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No exam records found.</td></tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
