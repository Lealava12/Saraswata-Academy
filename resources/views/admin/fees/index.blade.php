@extends('admin.layouts.app')
@section('title', 'Fees')
@section('page-title', 'Student Fee Records')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-coin me-2"></i>Fee Records</span>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.export-csv', ['type' => 'fee']) }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV
            </a>
            <a href="{{ route('admin.reports.export-pdf', ['type' => 'fee']) }}" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>PDF
            </a>
            <a href="{{ route('admin.fees.create') }}" class="btn btn-accent btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Add Fee Entry
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table data-table table-hover w-100">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Receipt No</th>
                    <th>Student</th>
                    <th>Student ID</th>
                    <th>Class</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Mode</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fees as $i => $fee)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td><span class="badge bg-secondary">{{ $fee->receipt_no }}</span></td>
                    <td>{{ $fee->student->name ?? '-' }}</td>
                    <td>{{ $fee->student->student_id ?? '-' }}</td>
                    <td>{{ $fee->classInfo->name ?? '-' }}</td>
                    <td class="fw-semibold">₹{{ number_format($fee->amount, 2) }}</td>
                    <td>{{ $fee->payment_date ? \Carbon\Carbon::parse($fee->payment_date)->format('d M, Y') : '-' }}</td>
                    <td>{{ $fee->payment_mode }}</td>
                    <td>{{ \Carbon\Carbon::parse($fee->due_date)->format('d M, Y') }}</td>
                    <td>
                        @if($fee->status === 'Paid')
                            <span class="badge badge-paid rounded-pill px-3">Paid</span>
                        @elseif($fee->status === 'Due')
                            <span class="badge badge-due rounded-pill px-3">Due</span>
                        @else
                            <span class="badge badge-overdue rounded-pill px-3">Overdue</span>
                            <div class="text-danger small mt-1 fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Late Payment!</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
