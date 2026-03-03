@extends('student.layouts.app')
@section('title', 'My Attendance')

@section('content')
<h5 class="fw-bold mb-4"><i class="bi bi-calendar-check me-2 text-primary"></i>Attendance Report</h5>

<!-- Summary -->
<div class="row g-2 g-md-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 fw-bold text-primary">{{ $total }}</div>
                <div class="text-muted small mt-1">Total Classes</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 fw-bold text-success">{{ $presentCount }}</div>
                <div class="text-muted small mt-1">Present</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 fw-bold text-danger">{{ $absentCount }}</div>
                <div class="text-muted small mt-1">Absent</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 fw-bold {{ $percentage >= 75 ? 'text-success' : 'text-danger' }}">{{ $percentage }}%</div>
                <div class="text-muted small mt-1">Attendance %</div>
                @if($percentage < 75)
                    <div class="badge bg-danger-subtle text-danger mt-1 small">Below 75%</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Subject-wise -->
@if($subjectSummary->count())
<div class="card mb-4">
    <div class="card-header fw-semibold">Subject-wise Summary</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Subject</th><th>Present</th><th>Absent</th><th>Total</th><th>%</th></tr></thead>
            <tbody>
                @foreach($subjectSummary as $subj => $data)
                @php $pct = $data['total'] > 0 ? round(($data['present']/$data['total'])*100, 1) : 0; @endphp
                <tr>
                    <td class="fw-medium">{{ $subj }}</td>
                    <td class="text-success">{{ $data['present'] }}</td>
                    <td class="text-danger">{{ $data['absent'] }}</td>
                    <td>{{ $data['total'] }}</td>
                    <td><span class="badge {{ $pct >= 75 ? 'bg-success' : 'bg-danger' }}">{{ $pct }}%</span></td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-auto">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}" placeholder="From">
            </div>
            <div class="col-6 col-md-auto">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}" placeholder="To">
            </div>
            <div class="col-12 col-md-auto d-flex gap-1 mt-2 mt-md-0">
                <button class="btn btn-sm btn-outline-primary flex-grow-1 flex-md-grow-0">Filter</button>
                <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-light flex-grow-1 flex-md-grow-0">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Detail Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-light text-center">
                    <tr><th width="30%">Date</th><th width="40%">Subject</th><th width="30%">Status</th></tr>
                </thead>
                <tbody class="text-center">
                    @php
                        $groupedRecords = $records->groupBy(function($r) {
                            $date = optional($r->attendance)->attendance_date;
                            return $date ? \Carbon\Carbon::parse($date)->format('d M Y') : '-';
                        });
                    @endphp
                    @forelse($groupedRecords as $date => $dateRecords)
                        @foreach($dateRecords as $index => $r)
                        <tr>
                            @if($index === 0)
                            <td rowspan="{{ $dateRecords->count() }}" class="fw-semibold bg-light">{{ $date }}</td>
                            @endif
                            <td>{{ $r->subject?->name ?? optional($r->attendance)->subjects?->pluck('name')->join(', ') ?: '-' }}</td>
                            <td>
                                <span class="badge {{ $r->status === 'Present' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3">
                                    {{ $r->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                    <tr><td colspan="3" class="text-center py-4 text-muted">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
