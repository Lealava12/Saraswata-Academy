@extends('admin.layouts.app')
@section('title', 'Fee History')
@section('page-title', 'Fee History – ' . $student->name)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-clock-history me-2"></i>
            Fee History for <strong>{{ $student->student_id }}</strong>
        </span>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.fees.create') }}?student_id={{ $student->id }}" class="btn btn-sm btn-accent">
                <i class="bi bi-plus-lg me-1"></i>Add Entry
            </a>
            <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Profile
            </a>
        </div>
    </div>

    @php
        // ✅ Correct logic: first due is joining_date + 1 month
        $installmentsDue = \App\Models\StudentFee::dueInstallmentsCount($student);
        $totalExpected   = \App\Models\StudentFee::expectedTotalTillToday($student);
        $totalPaid       = $student->fees->sum('amount');

        $remainingDue = max(0, $totalExpected - $totalPaid);
        $credit       = max(0, $totalPaid - $totalExpected);
    @endphp

    <div class="row g-3 px-3 pt-3">
        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Monthly Fee</h6>
                    <h4 class="mb-0">₹{{ number_format($student->monthly_fees ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Installments Due (Till Today)</h6>
                    <h4 class="mb-0">{{ $installmentsDue }}</h4>
                    <div class="small text-muted">First due = joining date + 1 month</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-light border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Expected Total (Till Today)</h6>
                    <h4 class="mb-0">₹{{ number_format($totalExpected, 2) }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card {{ $remainingDue > 0 ? 'bg-danger-subtle' : 'bg-success-subtle' }} border-0">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Remaining Due</h6>
                    <h4 class="mb-0 {{ $remainingDue > 0 ? 'text-danger' : 'text-success' }}">
                        ₹{{ number_format($remainingDue, 2) }}
                    </h4>

                    @if($credit > 0)
                        <div class="small text-success mt-1">
                            Extra Paid (Credit): ₹{{ number_format($credit, 2) }} (adjusts next months)
                        </div>
                    @endif
                </div>
            </div>
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
                    @php $runningPaid = 0; @endphp

                    @forelse($fees as $i => $fee)
                        @php
                            $runningPaid += (float)$fee->amount;
                            $runningRemaining = max(0, $totalExpected - $runningPaid);
                        @endphp

                        <tr>
                            <td>{{ $i + 1 }}</td>

                            <td>
                                <span class="badge bg-secondary">{{ $fee->receipt_no }}</span>
                            </td>

                            <td class="fw-semibold">₹{{ number_format($fee->amount, 2) }}</td>

                            <td>{{ optional($fee->payment_date)->format('Y-m-d') }}</td>

                            <td>{{ $fee->payment_mode }}</td>

                            <td>{{ optional($fee->due_date)->format('Y-m-d') }}</td>

                            <td>
                                @if($fee->status === 'Paid')
                                    <span class="badge bg-success rounded-pill px-3">Paid</span>
                                @elseif($fee->status === 'Due')
                                    <span class="badge bg-warning text-dark rounded-pill px-3">Due</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3">Overdue</span>
                                @endif
                            </td>

                             
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No fee payments found for this student.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection