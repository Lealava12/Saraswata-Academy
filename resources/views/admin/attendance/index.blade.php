@extends('admin.layouts.app')
@section('title', 'Attendance Records')
@section('page-title', 'Attendance Records')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-check-fill me-2"></i>Attendance Records</span>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.export-csv', ['type' => 'attendance']) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</a>
            <a href="{{ route('admin.reports.export-pdf', ['type' => 'attendance']) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            <a href="{{ route('admin.attendance.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg me-1"></i>Mark Attendance</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Class</th>
                        <th>Board</th>  
                        <th>Subjects</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $i => $a)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $a->attendance_date->format('d-m-Y') }}</td>
                        <td>{{ $a->classInfo->name ?? '-' }}</td>
                          <td>{{ $a->classInfo?->board?->name ?? '-' }}</td>
                        <td>
                            @foreach($a->subjects as $subject)
                                <span class="badge bg-info">{{ $subject->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('admin.attendance.show', $a->id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection