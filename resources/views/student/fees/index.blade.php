@extends('student.layouts.app')
@section('title', 'My Fee History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold"><i class="bi bi-cash-coin me-2 text-success"></i>Fee History</h5>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;">
            <div class="small opacity-75">Total Paid</div>
            <div class="fs-3 fw-bold">₹{{ number_format($totalPaid) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;">
            <div class="small opacity-75">Due Amount</div>
            <div class="fs-3 fw-bold">₹{{ number_format($totalDue) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;">
            <div class="small opacity-75">Overdue</div>
            <div class="fs-3 fw-bold">₹{{ number_format($totalOverdue) }}</div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small fw-medium mb-1">From</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-auto">
                <label class="form-label small fw-medium mb-1">To</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-outline-primary">Filter</button>
                <a href="{{ route('student.fees') }}" class="btn btn-sm btn-light ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Receipt No</th><th>Class</th><th>Amount</th><th>Payment Date</th><th>Mode</th><th>Due Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($fees as $fee)
                    <tr>
                        <td><span class="badge bg-secondary">{{ $fee->receipt_no }}</span></td>
                        <td>{{ $fee->classInfo->name ?? '-' }}</td>
                        <td class="fw-semibold">₹{{ number_format($fee->amount) }}</td>
                        <td>{{ $fee->payment_date }}</td>
                        <td>{{ $fee->payment_mode }}</td>
                        <td>{{ $fee->due_date }}</td>
                        <td>
                            @if($fee->status === 'Paid')
                                <span class="badge badge-paid rounded-pill px-3">Paid</span>
                            @elseif($fee->status === 'Due')
                                <span class="badge badge-due rounded-pill px-3">Due</span>
                            @else
                                <span class="badge badge-overdue rounded-pill px-3">Overdue</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No fee records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
