@extends('admin.layouts.app')
@section('title', 'Exam Marks')
@section('page-title', 'Exam Marks')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-journal-text me-2"></i>
            Marks List – {{ $exam->classInfo->name ?? '' }} / {{ $exam->subject->name ?? '' }} – {{ \Carbon\Carbon::parse($exam->exam_date)->format('d M Y') }}
            <span class="text-muted small">(Full Marks: {{ $exam->full_marks }})</span>
        </span>
        <div class="d-flex gap-2">
    <a href="{{ route('admin.exams.marks.export-csv', $exam->id) }}" class="btn btn-sm btn-success">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
    </a>
    <a href="{{ route('admin.exams.marks.export-pdf', $exam->id) }}" class="btn btn-sm btn-danger">
        <i class="bi bi-file-earmark-pdf me-1"></i>PDF Report
    </a>
    <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <small class="text-muted">Total Students:</small>
                    <strong>{{ $marks->count() }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Average Marks:</small>
                    <strong>{{ number_format($marks->avg('marks_obtained'), 1) ?? 'N/A' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Highest Marks:</small>
                    <strong>{{ $marks->max('marks_obtained') ?? 'N/A' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">Lowest Marks:</small>
                    <strong>{{ $marks->min('marks_obtained') ?? 'N/A' }}</strong>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Marks Obtained</th>
                        <th>Percentage</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($marks as $i => $mark)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td><span class="badge bg-secondary">{{ $mark->student->student_id ?? 'N/A' }}</span></td>
                        <td>{{ $mark->student->roll_no ?? 'N/A' }}</td>
                        <td>{{ $mark->student->name ?? 'N/A' }}</td>
                        <td>
                            @if($mark->marks_obtained !== null)
                                {{ $mark->marks_obtained }}
                            @else
                                <span class="text-muted">Not entered</span>
                            @endif
                        </td>
                        <td>
                            @if($mark->marks_obtained !== null)
                                {{ number_format(($mark->marks_obtained / $exam->full_marks) * 100, 1) }}%
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($mark->marks_obtained !== null)
                                @php
                                    $percentage = ($mark->marks_obtained / $exam->full_marks) * 100;
                                @endphp
                                @if($percentage >= 75)
                                    <span class="badge bg-success">Distinction</span>
                                @elseif($percentage >= 60)
                                    <span class="badge bg-info">First Class</span>
                                @elseif($percentage >= 45)
                                    <span class="badge bg-warning">Second Class</span>
                                @elseif($percentage >= 33)
                                    <span class="badge bg-secondary">Pass</span>
                                @else
                                    <span class="badge bg-danger">Fail</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No marks entered yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection