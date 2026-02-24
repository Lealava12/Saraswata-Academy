@extends('admin.layouts.app')
@section('title', 'Pay Teacher Salary')
@section('page-title', 'Pay Teacher Salary')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-wallet2 me-2"></i>Pay Teacher Salary</span>
        <a href="{{ route('admin.teacher-salary.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.teacher-salary.store') }}" id="tSalForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Teacher <span class="text-danger">*</span></label>
                    <select name="teacher_id" id="teacherSel" class="form-select" required>
                        <option value="">-- Select Teacher --</option>
                        @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Payment Month <span class="text-danger">*</span></label>
                    <input type="month" name="payment_month" class="form-control" value="{{ date('Y-m') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">No. of Classes</label>
                    <input type="number" name="class_count" class="form-control" min="0" value="0">
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4">
                        <i class="bi bi-wallet2 me-2"></i>Pay Salary
                    </button>
                    <a href="{{ route('admin.teacher-salary.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

