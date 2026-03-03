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
        
        @php
            // Group details by student ID
            $detailsByStudent = $attendance->details->groupBy('student_id');
        @endphp

        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%" rowspan="2" class="align-middle">#</th>
                        <th width="15%" rowspan="2" class="align-middle">Student ID</th>
                        <th width="20%" rowspan="2" class="align-middle">Name</th>
                        <th width="10%" rowspan="2" class="align-middle">Roll No</th>
                        <th colspan="{{ $attendance->subjects->count() }}">Subjects</th>
                    </tr>
                    <tr>
                        @foreach($attendance->subjects as $subject)
                            <th>{{ $subject->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($detailsByStudent as $studentId => $details)
                    @php
                        $student = $details->first()->student;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><span class="badge bg-secondary">{{ $student->student_id ?? '-' }}</span></td>
                        <td>{{ $student->name ?? '-' }}</td>
                        <td>{{ $student->roll_no ?? '-' }}</td>
                        
                        @foreach($attendance->subjects as $subject)
                            @php
                                // Find detail for this specific subject
                                $subjectDetail = $details->firstWhere('subject_id', $subject->id);
                                
                                // Fallback for old records without subject_id
                                if (!$subjectDetail && $details->whereNull('subject_id')->count() > 0) {
                                    $subjectDetail = $details->whereNull('subject_id')->first();
                                }
                            @endphp
                            <td>
                                @if($subjectDetail)
                                    <span class="badge {{ $subjectDetail->status === 'Present' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3">
                                        {{ $subjectDetail->status }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr><td colspan="{{ 4 + $attendance->subjects->count() }}" class="text-center py-4 text-muted">No records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection