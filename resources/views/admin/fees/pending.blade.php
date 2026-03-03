@extends('admin.layouts.app')
@section('title', 'Pending Fees')
@section('page-title', 'Students with Pending Fees')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-warning-subtle border-warning shadow-sm">
            <div class="card-body py-3 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-3 me-3"></i>
                <div>
                    <h5 class="mb-0 fw-bold">Pending Payments Summary</h5>
                    <p class="mb-0 text-muted">A total of <strong>{{ $pendingStudents->count() }}</strong> students have outstanding balances.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Outstanding Student Dues</h5>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.fees.pending') }}" method="GET" class="d-flex gap-2">
                <select name="board_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- All Boards --</option>
                    @foreach($boards as $board)
                        <option value="{{ $board->id }}" {{ request('board_id') == $board->id ? 'selected' : '' }}>{{ $board->name }}</option>
                    @endforeach
                </select>
                <select name="class_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- All Classes --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.fees.create') }}" class="btn btn-accent btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Collect Fees
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Student ID</th>
                        <th>Board / Class</th>
                        <th>Monthly Fee</th>
                        <th>Expected (Till Today)</th>
                        <th>Total Paid</th>
                        <th>Remaining Due</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingStudents as $student)
                    @php
                        $isOverdue = $student->getOverdueStatus();
                        $balance = $student->getBalanceDue();
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $student->name }}</div>
                            <small class="text-muted">Roll: {{ $student->roll_no }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $student->student_id }}</span></td>
                        <td>
                            <div class="small">{{ $student->board->name ?? '-' }}</div>
                            <div class="fw-semibold">{{ $student->classInfo->name ?? '-' }}</div>
                        </td>
                        <td>₹{{ number_format($student->monthly_fees, 0) }}</td>
                        <td>₹{{ number_format($student->getExpectedFeesTillToday(), 0) }}</td>
                        <td class="text-success">₹{{ number_format($student->getTotalPaidFees(), 0) }}</td>
                        <td>
                            <span class="text-danger fw-bold fs-6">₹{{ number_format($balance, 2) }}</span>
                        </td>
                        <td>
                            @if($isOverdue)
                                <span class="badge bg-danger rounded-pill px-3">OVERDUE</span>
                                <div class="text-danger small mt-1"><i class="bi bi-clock-fill"></i> > 10 Days Late</div>
                            @else
                                <span class="badge bg-warning text-dark rounded-pill px-3">PENDING</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.fees.create', ['student_id' => $student->id]) }}" class="btn btn-sm btn-outline-primary" title="Collect Fee">
                                    <i class="bi bi-cash-coin"></i>
                                </a>
                                <a href="{{ route('admin.students.fee-history', $student->id) }}" class="btn btn-sm btn-outline-info" title="History">
                                    <i class="bi bi-list-ul"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
