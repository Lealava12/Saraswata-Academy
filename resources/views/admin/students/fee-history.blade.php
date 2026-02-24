@extends('admin.layouts.app')
@section('title', 'Fee History')
@section('page-title', 'Fee History – ' . $student->name)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i>Fee History for <strong>{{ $student->student_id }}</strong></span>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.fees.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-accent">
                <i class="bi bi-plus-lg me-1"></i>Add Entry
            </a>
            <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Profile
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Receipt No</th>
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
                        <td class="fw-semibold">₹{{ number_format($fee->amount, 2) }}</td>
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
