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
                <select name="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium small">Student</label>
                <select name="student_id" class="form-select form-select-sm">
                    <option value="">All Students</option>
                    @foreach($students as $s)
                    <option value="{{ $s->id }}" {{ request('student_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
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

<div class="d-flex gap-2 mb-3 justify-content-end">
    <a href="{{ route('admin.reports.export-csv', array_merge(['type'=>'attendance'], request()->query())) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Export CSV</a>
    <a href="{{ route('admin.reports.export-pdf', array_merge(['type'=>'attendance'], request()->query())) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table data-table table-hover w-100">
            <thead class="table-light"><tr><th>#</th><th>Date</th><th>Class</th><th>Subject</th><th>Total</th><th>Present</th><th>Absent</th><th>%</th></tr></thead>
            <tbody>
                @foreach($rows as $i => $row)
                @php
                    $p=$row['present']; $t=$row['total'];
                    $pct = $t > 0 ? round(($p/$t)*100,1) : 0;
                @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['class'] }}</td>
                    <td>{{ $row['subject'] }}</td>
                    <td>{{ $t }}</td>
                    <td class="text-success fw-semibold">{{ $p }}</td>
                    <td class="text-danger fw-semibold">{{ $row['absent'] }}</td>
                    <td><span class="badge {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}">{{ $pct }}%</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
