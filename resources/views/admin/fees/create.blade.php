@extends('admin.layouts.app')
@section('title', 'Add Fee Entry')
@section('page-title', 'Add Fee Entry')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-coin me-2"></i>Add Fee Entry</span>
        <a href="{{ route('admin.fees.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.fees.store') }}" id="feeForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Student <span class="text-danger">*</span></label>
                    <select name="student_id" id="studentSelect" class="form-select" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $s)
                        <option value="{{ $s->id }}" data-fee="{{ $s->classInfo->monthly_fee ?? 0 }}">
                            {{ $s->name }} ({{ $s->student_id }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="amountField" class="form-control" step="0.01" required>
                    <div id="feeHint" class="text-muted small mt-1"></div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Payment Mode <span class="text-danger">*</span></label>
                    <select name="payment_mode" class="form-select" required>
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI</option>
                        <option value="Bank">Bank Transfer</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>
                <!-- Hidden MPIN -->
                <input type="hidden" name="mpin" id="hiddenMpin">
                <div class="col-12 mt-2">
                    <button type="button" class="btn btn-accent px-4" id="feeSubmitBtn">
                        <i class="bi bi-lock-fill me-2"></i>Verify MPIN & Save
                    </button>
                    <a href="{{ route('admin.fees.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Show class fee hint
$('#studentSelect').on('change', function() {
    const fee = $(this).find(':selected').data('fee');
    if (fee > 0) {
        $('#feeHint').text('Class monthly fee: ₹' + fee);
        $('#amountField').val(fee);
    } else {
        $('#feeHint').text('');
    }
});

// MPIN gate before submit
$('#feeSubmitBtn').on('click', function() {
    // Show MPIN modal
    new bootstrap.Modal(document.getElementById('mpinModal')).show();
    $('#mpinConfirmBtn').off('click').on('click', function() {
        const mpin = $('#mpinInput').val();
        $.post('{{ route("admin.fees.verify-mpin") }}', {mpin: mpin}, function(res) {
            if (res.success) {
                bootstrap.Modal.getInstance(document.getElementById('mpinModal')).hide();
                $('#hiddenMpin').val(mpin);
                $('#feeForm').submit();
            } else {
                $('#mpinError').removeClass('d-none').text(res.message);
            }
        });
    });
});
</script>
@endpush
