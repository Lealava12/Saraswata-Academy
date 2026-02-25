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

        </div>
    </div>
</div>
@endsection