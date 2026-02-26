@extends('admin.layouts.app')
@section('title', 'Pay Staff Salary')
@section('page-title', 'Pay Staff Salary')

@section('content')
<div class="card" style="max-width:700px">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-wallet2 me-2"></i>Pay Staff Salary</span>
        <a href="{{ route('admin.staff-salary.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.staff-salary.store') }}" id="staffSalForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Staff Member <span class="text-danger">*</span></label>
                    <select name="staff_id" id="staffSel" class="form-select" required>
                        <option value="">-- Select Staff --</option>
                        @foreach($staff as $s)
                        <option value="{{ $s->id }}" data-salary="{{ $s->monthly_salary }}">{{ $s->name }} ({{ $s->role }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="staffAmtField" class="form-control" step="0.01" required>
                    <div id="staffSalHint" class="text-muted small mt-1"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Payment Month <span class="text-danger">*</span></label>
                    <input type="month" name="payment_month" class="form-control" value="{{ date('Y-m') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4">
                        <i class="bi bi-wallet2 me-2"></i>Pay Salary
                    </button>
                    <a href="{{ route('admin.staff-salary.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Auto fill salary
    $('#staffSel').on('change', function() {
        const sal = $(this).find(':selected').data('salary');
        if (sal > 0) {
            $('#staffAmtField').val(sal);
            $('#staffSalHint').text('Default monthly: ₹' + sal);
        } else {
            $('#staffSalHint').text('');
        }
    });

    // Confirm before submit
    $('#staffSalForm').on('submit', function(e) {
        e.preventDefault(); // Stop normal submit

        if (confirm("Are you sure you want to pay this salary?")) {
            this.submit(); // Submit if confirmed
        }
        // If cancel -> do nothing (form will not submit)
    });

});
</script>
@endpush
