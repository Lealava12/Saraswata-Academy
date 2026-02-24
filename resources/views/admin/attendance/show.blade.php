@extends('admin.layouts.app')
@section('title', 'Attendance Detail')
@section('page-title', 'Attendance Detail')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-calendar-check-fill me-2"></i>
            {{ $attendance->classInfo->name ?? '' }} – 
            @foreach($attendance->subjects as $subject)
                {{ $subject->name }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
            – {{ $attendance->attendance_date->format('d-m-Y') }}
        </span>
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        @php
            $present = $attendance->details->where('status','Present')->count();
            $absent  = $attendance->details->where('status','Absent')->count();
            $total   = $attendance->details->count();
        @endphp
        <div class="d-flex gap-3 mb-3">
            <span class="badge bg-success px-3 py-2 fs-6">✓ Present: {{ $present }}</span>
            <span class="badge bg-danger px-3 py-2 fs-6">✗ Absent: {{ $absent }}</span>
            <span class="badge bg-secondary px-3 py-2 fs-6">Total: {{ $total }}</span>
        </div>
        
        <div class="mb-3">
            <strong>Subjects:</strong>
            @foreach($attendance->subjects as $subject)
                <span class="badge bg-info">{{ $subject->name }}</span>
            @endforeach
        </div>
        
        <table class="table table-hover">
            <thead class="table-light">
                <tr><th>#</th><th>Student ID</th><th>Name</th><th>Roll No</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($attendance->details as $i => $d)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td><span class="badge bg-secondary">{{ $d->student->student_id ?? '-' }}</span></td>
                    <td>{{ $d->student->name ?? '-' }}</td>
                    <td>{{ $d->student->roll_no ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $d->status === 'Present' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3">
                            {{ $d->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection