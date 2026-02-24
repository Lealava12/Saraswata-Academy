@extends('admin.layouts.app')
@section('title', 'Exam Report')
@section('page-title', 'Exam Result Report')

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-medium">Class</label>
                <select name="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Subject</label>
                <select name="subject_id" class="form-select form-select-sm">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium">From</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium">To</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-accent w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mb-3">
    <a href="{{ route('admin.reports.export-csv', array_merge(['type'=>'exam'], request()->query())) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV</a>
    <a href="{{ route('admin.reports.export-pdf', array_merge(['type'=>'exam'], request()->query())) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table data-table table-hover w-100">
            <thead class="table-light"><tr><th>#</th><th>Date</th><th>Class</th><th>Subject</th><th>Student</th><th>Full Marks</th><th>Obtained</th><th>%</th><th>Grade</th></tr></thead>
            <tbody>
                @foreach($marks as $i => $m)
                @php
                    $full = $m->exam->full_marks ?? 0;
                    $pct = $full > 0 ? round(($m->marks_obtained/$full)*100, 1) : 0;
                    $grade = $pct>=90?'A+':($pct>=75?'A':($pct>=60?'B':($pct>=45?'C':'F')));
                @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ optional($m->exam)->exam_date ?? '-' }}</td>
                    <td>{{ optional(optional($m->exam)->classInfo)->name ?? '-' }}</td>
                    <td>{{ optional(optional($m->exam)->subject)->name ?? '-' }}</td>
                    <td>{{ optional($m->student)->name ?? '-' }}</td>
                    <td>{{ $full }}</td>
                    <td class="fw-semibold">{{ $m->marks_obtained }}</td>
                    <td>{{ $pct }}%</td>
                    <td><span class="badge {{ $pct>=75?'bg-success':($pct>=45?'bg-warning text-dark':'bg-danger') }}">{{ $grade }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
