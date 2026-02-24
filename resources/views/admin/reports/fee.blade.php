@extends('admin.layouts.app')
@section('title', 'Fee Report')
@section('page-title', 'Fee Collection Report')

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

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;">
            <div class="small opacity-75">Total Paid</div>
            <div class="fs-2 fw-bold">₹{{ number_format($totalPaid) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;">
            <div class="small opacity-75">Total Due</div>
            <div class="fs-2 fw-bold">₹{{ number_format($totalDue) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;">
            <div class="small opacity-75">Total Overdue</div>
            <div class="fs-2 fw-bold">₹{{ number_format($totalOverdue) }}</div>
        </div>
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
                    <td>{{ $fee->classInfo->name ?? '-' }}</td>
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
