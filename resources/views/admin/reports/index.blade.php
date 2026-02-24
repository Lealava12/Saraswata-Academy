@extends('admin.layouts.app')
@section('title', 'Attendance Report')
@section('page-title', 'Attendance Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bar-chart-line me-2"></i>Attendance Report</span>
                <div class="d-flex gap-2">
                    @if(request()->has('class_id') || request()->has('from_date'))
                    <a href="{{ route('admin.reports.export-attendance-csv', request()->all()) }}" 
                       class="btn btn-sm btn-outline-success">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV
                    </a>
                    <a href="{{ route('admin.reports.export-attendance-pdf', request()->all()) }}" 
                       class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('admin.reports.attendance') }}" class="mb-4" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Class</label>
                            <select name="class_id" id="class_id" class="form-select">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Subject</label>
                            <select name="subject_id" id="subject_id" class="form-select">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Student</label>
                            <select name="student_id" id="student_id" class="form-select">
                                <option value="">All Students</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->student_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-medium">From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-medium">To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-accent me-2">
                                <i class="bi bi-search me-1"></i>Generate Report
                            </button>
                            <a href="{{ route('admin.reports.attendance') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>

                @if(request()->has('class_id') || request()->has('from_date'))
                    <!-- Statistics Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Records</h6>
                                    <h3 class="mb-0">{{ $totalRecords }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Present</h6>
                                    <h3 class="mb-0">{{ $totalPresent }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Absent</h6>
                                    <h3 class="mb-0">{{ $totalAbsent }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Attendance %</h6>
                                    <h3 class="mb-0">
                                        @if($totalRecords > 0)
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
                    @if(count($attendances) > 0)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Attendance Trend</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                    @endif

                    <!-- Detailed Report Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Class</th>
                                    <th>Subjects</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Roll No</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    @php
                                        $subjects = $attendance->subjects->pluck('name')->implode(', ');
                                    @endphp
                                    @foreach($attendance->details as $detail)
                                        <tr>
                                            <td>{{ $loop->parent->index * $loop->count + $loop->index + 1 }}</td>
                                            <td>{{ $attendance->attendance_date->format('d-m-Y') }}</td>
                                            <td>{{ $attendance->classInfo->name ?? '-' }}</td>
                                            <td>
                                                @foreach($attendance->subjects as $subject)
                                                    <span class="badge bg-info">{{ $subject->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $detail->student->student_id ?? '-' }}</span>
                                            </td>
                                            <td>{{ $detail->student->name ?? '-' }}</td>
                                            <td class="text-center">{{ $detail->student->roll_no ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ $detail->status === 'Present' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3">
                                                    {{ $detail->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            No attendance records found for the selected criteria.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary by Student (if no specific student selected) -->
                    @if(!request('student_id') && count($attendances) > 0)
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
                                        <th>Total Present</th>
                                        <th>Total Absent</th>
                                        <th>Total Days</th>
                                        <th>Attendance %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $studentSummary = [];
                                        foreach($attendances as $attendance) {
                                            foreach($attendance->details as $detail) {
                                                $studentId = $detail->student_id;
                                                if(!isset($studentSummary[$studentId])) {
                                                    $studentSummary[$studentId] = [
                                                        'name' => $detail->student->name ?? 'Unknown',
                                                        'student_id' => $detail->student->student_id ?? '-',
                                                        'roll_no' => $detail->student->roll_no ?? '-',
                                                        'present' => 0,
                                                        'absent' => 0,
                                                        'total' => 0
                                                    ];
                                                }
                                                
                                                if($detail->status === 'Present') {
                                                    $studentSummary[$studentId]['present']++;
                                                } else {
                                                    $studentSummary[$studentId]['absent']++;
                                                }
                                                $studentSummary[$studentId]['total']++;
                                            }
                                        }
                                    @endphp
                                    
                                    @foreach($studentSummary as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="badge bg-secondary">{{ $student['student_id'] }}</span></td>
                                        <td>{{ $student['name'] }}</td>
                                        <td class="text-center">{{ $student['roll_no'] }}</td>
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
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-bar-chart-line display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Select filters to generate attendance report</h5>
                        <p class="text-muted">Choose class, subject, date range and click "Generate Report"</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
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
                }
            });
        } else {
            studentSelect.html('<option value="">All Students</option>');
        }
    });

    // Initialize chart if data exists
    @if(count($attendances) > 0)
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Present',
                data: {!! json_encode($chartPresent) !!},
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Absent',
                data: {!! json_encode($chartAbsent) !!},
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