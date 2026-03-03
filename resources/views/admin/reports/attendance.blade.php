@extends('admin.layouts.app')
@section('title', 'Attendance Report')
@section('page-title', 'Attendance Report')

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-medium small">Class</label>
                <select name="class_id" id="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->board ? ' ('. $c->board->name .')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium small">Subject</label>
                <select name="subject_id" class="form-select form-select-sm">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium small">Student</label>
                <select name="student_id" id="student_id" class="form-select form-select-sm">
                    <option value="">All Students</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->student_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small">From</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small">To</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-accent w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Export Buttons - Fixed Routes -->
<div class="d-flex gap-2 mb-3 justify-content-end">
    <a href="{{ route('admin.reports.export-attendance-csv', request()->query()) }}" class="btn btn-sm btn-outline-success">
        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
    </a>
    <a href="{{ route('admin.reports.export-attendance-pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger">
        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
    </a>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Records</h6>
                <h3 class="mb-0">{{ $totalRecords ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Present</h6>
                <h3 class="mb-0">{{ $totalPresent ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Total Absent</h6>
                <h3 class="mb-0">{{ $totalAbsent ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Attendance %</h6>
                <h3 class="mb-0">
                    @if(($totalRecords ?? 0) > 0)
                        {{ round(($totalPresent / $totalRecords) * 100, 2) }}%
                    @else
                        0%
                    @endif
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
@if(isset($attendances) && count($attendances) > 0)
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Attendance Trend</h6>
    </div>
    <div class="card-body">
        <canvas id="attendanceChart" style="height: 300px;"></canvas>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body p-0">
        <table class="table data-table table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Class</th>
                    <th>Subjects</th>
                    <th>Total</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>%</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances ?? [] as $i => $attendance)
                
                @php
                    $present = $attendance->details->where('status', 'Present')->count();
                    $absent = $attendance->details->where('status', 'Absent')->count();
                    $total = $attendance->details->count();
                    $pct = $total > 0 ? round(($present/$total)*100,1) : 0;
                    $subjectNames = $attendance->subjects->pluck('name')->implode(', ');
                @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $attendance->attendance_date->format('d-m-Y') }}</td>
                    <td>{{ $attendance->classInfo->name ?? '-' }} {{ optional($attendance->classInfo->board)->name ? '('.$attendance->classInfo->board->name.')' : '' }}</td>
                    <td>
                        @foreach($attendance->subjects as $subject)
                            <span class="badge bg-info">{{ $subject->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $total }}</td>
                    <td class="text-success fw-semibold">{{ $present }}</td>
                    <td class="text-danger fw-semibold">{{ $absent }}</td>
                    <td>
                        <span class="badge {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}">
                            {{ $pct }}%
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.attendance.show', $attendance->id) }}" 
                           class="btn btn-sm btn-outline-info" target="_blank">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        No attendance records found for the selected criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Student-wise Summary (if no specific student selected) -->
@if(!request('student_id') && isset($attendances) && count($attendances) > 0)
<div class="mt-4">
    <h6 class="mb-3">Student-wise Summary</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Roll No</th>
                    <th>Subject</th>
                    <th>Total Present</th>
                    <th>Total Absent</th>
                    <th>Total Days/Classes</th>
                    <th>Attendance %</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $studentSummary = [];
                    foreach($attendances as $attendance) {
                        foreach($attendance->details as $detail) {
                            $studentId = $detail->student_id;
                            // Use subject_id to distinguish records. If null, use a default fallback
                            $subjectId = $detail->subject_id ?? 'all'; 
                            
                            $key = $studentId . '_' . $subjectId;
                            
                            if(!isset($studentSummary[$key])) {
                                $studentSummary[$key] = [
                                    'name' => $detail->student->name ?? 'Unknown',
                                    'student_id' => $detail->student->student_id ?? '-',
                                    'roll_no' => $detail->student->roll_no ?? '-',
                                    'subject_name' => $detail->subject->name ?? 'All Subjects (Legacy)',
                                    'present' => 0,
                                    'absent' => 0,
                                    'total' => 0
                                ];
                            }
                            
                            if($detail->status === 'Present') {
                                $studentSummary[$key]['present']++;
                            } else {
                                $studentSummary[$key]['absent']++;
                            }
                            $studentSummary[$key]['total']++;
                        }
                    }
                @endphp
                
                @foreach($studentSummary as $index => $student)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="badge bg-secondary">{{ $student['student_id'] }}</span></td>
                    <td>{{ $student['name'] }}</td>
                    <td class="text-center">{{ $student['roll_no'] }}</td>
                    <td>{{ $student['subject_name'] }}</td>
                    <td class="text-success fw-bold">{{ $student['present'] }}</td>
                    <td class="text-danger fw-bold">{{ $student['absent'] }}</td>
                    <td>{{ $student['total'] }}</td>
                    <td>
                        @if($student['total'] > 0)
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ ($student['present'] / $student['total']) * 100 }}%">
                                    {{ round(($student['present'] / $student['total']) * 100, 1) }}%
                                </div>
                            </div>
                        @else
                            0%
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Load students based on class selection
    $('#class_id').on('change', function() {
        const classId = $(this).val();
        const studentSelect = $('#student_id');
        
        if (classId) {
            $.ajax({
                url: '{{ route("admin.reports.get-students-by-class") }}',
                type: 'POST',
                data: {
                    class_id: classId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(students) {
                    studentSelect.html('<option value="">All Students</option>');
                    students.forEach(function(student) {
                        studentSelect.append(`<option value="${student.id}">${student.name} (${student.student_id})</option>`);
                    });
                },
                error: function(xhr) {
                    console.error('Error loading students:', xhr);
                }
            });
        } else {
            studentSelect.html('<option value="">All Students</option>');
        }
    });

    // Initialize chart if data exists
    @if(isset($attendances) && count($attendances) > 0)
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels ?? []) !!},
            datasets: [{
                label: 'Present',
                data: {!! json_encode($chartPresent ?? []) !!},
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Absent',
                data: {!! json_encode($chartAbsent ?? []) !!},
                borderColor: 'rgb(220, 53, 69)',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    @endif

    // Date range validation
    $('input[name="to_date"]').on('change', function() {
        const fromDate = $('input[name="from_date"]').val();
        const toDate = $(this).val();
        
        if (fromDate && toDate && toDate < fromDate) {
            alert('To date cannot be less than from date');
            $(this).val('');
        }
    });

    $('input[name="from_date"]').on('change', function() {
        const toDate = $('input[name="to_date"]').val();
        const fromDate = $(this).val();
        
        if (toDate && fromDate && toDate < fromDate) {
            alert('From date cannot be greater than to date');
            $(this).val('');
        }
    });
});
</script>
@endpush