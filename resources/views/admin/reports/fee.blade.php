@extends('admin.layouts.app')
@section('title', 'Fee Report')
@section('page-title', 'Fee Collection Report')

@section('content')
<!-- Analytics Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;">
            <div class="small opacity-75">Estimated Total Revenue</div>
            <div class="fs-2 fw-bold">₹{{ number_format($totalRevenue) }}</div>
            <div class="small mt-1 text-white-50">Combined month-wise expected</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;">
            <div class="small opacity-75">Actual Fees Collected</div>
            <div class="fs-2 fw-bold">₹{{ number_format($totalCollected) }}</div>
            <div class="small mt-1 text-white-50">Total payments received</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;">
            <div class="small opacity-75">Pending Balance</div>
            <div class="fs-2 fw-bold">₹{{ number_format($totalPending) }}</div>
            <div class="small mt-1 text-white-50">Regular outstanding dues</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;">
            <div class="small opacity-75">Critical Overdue</div>
            <div class="fs-2 fw-bold">₹{{ number_format($globalOverdueAmount) }}</div>
            <div class="small mt-1 text-white-50"><i class="bi bi-clock-history me-1"></i>{{ $globalOverdueCount }} Students > 10 Days</div>
        </div>
    </div>
</div>

@php
    $collectionPercent = $totalRevenue > 0 ? ($totalCollected / $totalRevenue) * 100 : 0;
@endphp
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-2">
            <span class="fw-bold">Collection Efficiency (Target ₹{{ number_format($totalRevenue) }})</span>
            <span class="fw-bold text-success">{{ number_format($collectionPercent, 1) }}%</span>
        </div>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $collectionPercent }}%" aria-valuenow="{{ $collectionPercent }}" aria-valuemin="0" aria-valuemax="100">Paid</div>
            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ 100 - $collectionPercent }}%" aria-valuenow="{{ 100 - $collectionPercent }}" aria-valuemin="0" aria-valuemax="100">Pending</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-medium small">Class</label>
                <select name="class_id" class="form-select form-select-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->board ? ' ('. $c->board->name .')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-medium small">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option {{ request('status')=='Paid' ? 'selected':'' }}>Paid</option>
                    <option {{ request('status')=='Due' ? 'selected':'' }}>Due</option>
                    <option {{ request('status')=='Overdue' ? 'selected':'' }}>Overdue</option>
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
            <div class="col-md-3">
                <button class="btn btn-sm btn-accent"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('admin.reports.export-csv', array_merge(['type'=>'fee'], request()->query())) }}" class="btn btn-sm btn-outline-success ms-1"><i class="bi bi-file-earmark-spreadsheet"></i></a>
                <a href="{{ route('admin.reports.export-pdf', array_merge(['type'=>'fee'], request()->query())) }}" class="btn btn-sm btn-outline-danger ms-1"><i class="bi bi-file-earmark-pdf"></i></a>
            </div>
        </form>
    </div>
</div>



<div class="card">
    <div class="card-body p-0">
        <table class="table data-table table-hover w-100">
            <thead class="table-light"><tr><th>#</th><th>Receipt No</th><th>Student</th><th>Class</th><th>Amount</th><th>Payment Date</th><th>Due Date</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($fees as $i => $fee)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td><span class="badge bg-secondary">{{ $fee->receipt_no }}</span></td>
                    <td>{{ $fee->student->name ?? '-' }}</td>
                    <td>{{ optional($fee->classInfo)->name ?? '-' }} {{ optional($fee->classInfo->board)->name ? '('.$fee->classInfo->board->name.')' : '' }}</td>
                    <td class="fw-semibold">₹{{ number_format($fee->amount) }}</td>
                    <td>{{ $fee->payment_date }}</td>
                    <td>{{ $fee->due_date }}</td>
                    <td><span class="badge rounded-pill px-3 {{ $fee->status==='Paid' ? 'badge-paid' : ($fee->status==='Due' ? 'badge-due' : 'badge-overdue') }}">{{ $fee->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
