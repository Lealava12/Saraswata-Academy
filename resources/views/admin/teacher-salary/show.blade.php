@extends('admin.layouts.app')
@section('title', 'Salary Details')
@section('page-title', 'Salary Details')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2"></i>Salary Details</span>
        <a href="{{ route('admin.teacher-salary.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card-body">
        <div class="row g-4">

            <div class="col-md-4">
                <label class="fw-bold">Teacher</label>
                <div>{{ $teacherSalary->teacher->name ?? '-' }}</div>
            </div>

            <div class="col-md-4">
                <label class="fw-bold">Payment Month</label>
                <div>{{ $teacherSalary->payment_month }}</div>
            </div>

            <div class="col-md-4">
                <label class="fw-bold">Payment Date</label>
                <div>{{ \Carbon\Carbon::parse($teacherSalary->payment_date)->format('d M Y') }}</div>
            </div>

            <div class="col-md-4">
                <label class="fw-bold">Amount Paid</label>
                <div class="fs-5 fw-semibold text-success">
                    ₹{{ number_format($teacherSalary->amount, 2) }}
                </div>
            </div>

            <div class="col-md-4">
                <label class="fw-bold">No. of Classes Attempted</label>
                <div>{{ $teacherSalary->class_count ?? 0 }}</div>
            </div>

            <div class="col-md-4">
                <label class="fw-bold">Created At</label>
                <div>{{ $teacherSalary->created_at->format('d M Y h:i A') }}</div>
            </div>

            <!-- Breakdown Table -->
            @if($teacherSalary->breakdown && is_array($teacherSalary->breakdown))
            <div class="col-12 mt-4">
                <div class="card bg-light border-0">
                    <div class="card-header fw-bold bg-secondary text-white">Calculation Breakdown</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th>Class</th>
                                        <th>Fee per Class (₹)</th>
                                        <th>No. of Classes</th>
                                        <th>Total (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($teacherSalary->breakdown as $item)
                                    <tr>
                                        <td>{{ $item['class_name'] ?? '-' }}</td>
                                        <td class="text-center">₹{{ number_format($item['fee'] ?? 0, 2) }}</td>
                                        <td class="text-center">{{ $item['count'] ?? 0 }}</td>
                                        <td class="text-end fw-semibold">₹{{ number_format($item['total'] ?? 0, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="2" class="text-end">Grand Total:</td>
                                        <td class="text-center">{{ $teacherSalary->class_count ?? 0 }}</td>
                                        <td class="text-end text-success">₹{{ number_format($teacherSalary->amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection