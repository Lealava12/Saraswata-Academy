@extends('admin.layouts.app')
@section('title', 'Add Fee Entry')
@section('page-title', 'Add Fee Entry')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-coin me-2"></i>Add Fee Entry</span>
        <a href="{{ route('admin.fees.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.fees.store') }}" id="feeForm">
            @csrf

            <div class="row g-3">
   
                {{-- Board --}}
                <div class="col-md-3">
                    <label class="form-label fw-medium">Board <span class="text-danger">*</span></label>
                    <select name="board_id" id="boardSelect" class="form-select" required>
                        <option value="">-- Select Board --</option>
                        @foreach($boards as $b)
                            <option value="{{ $b->id }}"
                                @selected(old('board_id', $selectedBoardId) == $b->id)>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Class --}}
                <div class="col-md-3">
                    <label class="form-label fw-medium">Class <span class="text-danger">*</span></label>
                    <select name="class_id" id="classSelect" class="form-select" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}"
                                data-board="{{ $c->board_id }}"
                                @selected(old('class_id', $selectedClassId) == $c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Student --}}
                <div class="col-md-3">
                    <label class="form-label fw-medium">Student <span class="text-danger">*</span></label>
                    <select name="student_id" id="studentSelect" class="form-select" required>
                        <option value="">-- Select Student --</option>

                        @foreach($students as $s)
                            @php $paid = (float)($paidMap[$s->id] ?? 0); @endphp
                            <option value="{{ $s->id }}"
                                data-board="{{ $s->board_id }}"
                                data-class="{{ $s->class_id }}"
                                data-fee="{{ $s->monthly_fees ?? 0 }}"
                                data-join="{{ $s->joining_date }}"
                                data-paid="{{ $paid }}"
                                @selected(old('student_id', $selectedStudentId) == $s->id)
                            >
                                {{ $s->name }} ({{ $s->student_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Amount --}}
                <div class="col-md-3">
                    <label class="form-label fw-medium">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="amountField" class="form-control" step="0.01"
                           value="{{ old('amount') }}" required>
                    <div id="feeHint" class="text-muted small mt-1"></div>
                    <div id="balanceHint" class="small mt-1 d-none"></div>
                </div>

                {{-- Payment Mode --}}
                <div class="col-md-4">
                    <label class="form-label fw-medium">Payment Mode <span class="text-danger">*</span></label>
                    <select name="payment_mode" class="form-select" required>
                        <option value="Cash" @selected(old('payment_mode')=='Cash')>Cash</option>
                        <option value="UPI" @selected(old('payment_mode')=='UPI')>UPI</option>
                        <option value="Bank" @selected(old('payment_mode')=='Bank')>Bank</option>
                    </select>
                </div>

                {{-- Payment Date --}}
                <div class="col-md-4">
                    <label class="form-label fw-medium">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control"
                           value="{{ old('payment_date', date('Y-m-d')) }}" required>
                </div>

                {{-- Due Date --}}
                <div class="col-md-4">
                    <label class="form-label fw-medium">Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" id="dueDateField" class="form-control"
                           value="{{ old('due_date') }}" required>
                </div>

                <div class="col-12 mt-2">
                    <button type="button" class="btn btn-primary px-4" id="submitBtn">
                        <i class="bi bi-save2 me-2"></i>Save Fee Entry
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
(function () {
    const boardSelect   = document.getElementById('boardSelect');
    const classSelect   = document.getElementById('classSelect');
    const studentSelect = document.getElementById('studentSelect');
    const amountField   = document.getElementById('amountField');
    const feeHint       = document.getElementById('feeHint');
    const balanceHint   = document.getElementById('balanceHint');
    const dueDateField  = document.getElementById('dueDateField');
    const submitBtn     = document.getElementById('submitBtn');

    function toggleOption(opt, show) {
        opt.style.display = show ? '' : 'none';
    }

    function filterClassesByBoard(boardId) {
        for (const opt of classSelect.options) {
            if (!opt.value) continue;
            const optBoard = opt.getAttribute('data-board');
            toggleOption(opt, !boardId || String(optBoard) === String(boardId));
        }
    }

    function filterStudents(boardId, classId) {
        for (const opt of studentSelect.options) {
            if (!opt.value) continue;
            const sBoard = opt.getAttribute('data-board');
            const sClass = opt.getAttribute('data-class');
            const show = (!boardId || String(sBoard) === String(boardId)) &&
                         (!classId || String(sClass) === String(classId));
            toggleOption(opt, show);
        }
    }

    // first due = join + 1 month
    function setNextDueDate(joinDateStr) {
        if (!joinDateStr) { dueDateField.value = ''; return; }
        const join = new Date(joinDateStr);

        let due = new Date(join.getFullYear(), join.getMonth() + 1, join.getDate());
        const expectedMonth = (join.getMonth() + 1) % 12;
        if (due.getMonth() !== expectedMonth) {
            due = new Date(join.getFullYear(), join.getMonth() + 2, 0);
        }

        const yyyy = due.getFullYear();
        const mm = String(due.getMonth() + 1).padStart(2, '0');
        const dd = String(due.getDate()).padStart(2, '0');
        dueDateField.value = `${yyyy}-${mm}-${dd}`;
    }

    function countDueInstallments(joinDateStr) {
        if (!joinDateStr) return 0;
        const join = new Date(joinDateStr);
        const today = new Date();
        today.setHours(0,0,0,0);

        let due = new Date(join.getFullYear(), join.getMonth() + 1, join.getDate());
        const expectedMonth = (join.getMonth() + 1) % 12;
        if (due.getMonth() !== expectedMonth) {
            due = new Date(join.getFullYear(), join.getMonth() + 2, 0);
        }
        due.setHours(0,0,0,0);

        let count = 0;
        while (due <= today) {
            count++;
            let next = new Date(due.getFullYear(), due.getMonth() + 1, join.getDate());
            const expNextMonth = (due.getMonth() + 1) % 12;
            if (next.getMonth() !== expNextMonth) {
                next = new Date(due.getFullYear(), due.getMonth() + 2, 0);
            }
            next.setHours(0,0,0,0);
            due = next;
            if (count > 240) break;
        }
        return count;
    }

    function updateFromStudent() {
        feeHint.textContent = '';
        balanceHint.textContent = '';
        balanceHint.classList.add('d-none');
        balanceHint.classList.remove('text-danger','text-success');

        const opt = studentSelect.options[studentSelect.selectedIndex];
        if (!opt || !opt.value) return;

        const fee  = parseFloat(opt.getAttribute('data-fee') || '0') || 0;
        const join = opt.getAttribute('data-join') || '';
        const paid = parseFloat(opt.getAttribute('data-paid') || '0') || 0;

        if (fee > 0) {
            feeHint.textContent = `Student monthly fee: ₹${fee}`;
            if (!amountField.value) amountField.value = fee;
        }

        const dueMonths = countDueInstallments(join);
        const expected = dueMonths * fee;
        const balance = expected - paid;

        if (fee > 0) {
            if (balance > 0.01) {
                balanceHint.classList.remove('d-none');
                balanceHint.classList.add('text-danger');
                balanceHint.textContent = `Total Due: ₹${balance.toFixed(2)} (${dueMonths} installment(s) due)`;
            } else if (balance < -0.01) {
                balanceHint.classList.remove('d-none');
                balanceHint.classList.add('text-success');
                balanceHint.textContent = `Credit: ₹${Math.abs(balance).toFixed(2)} (adjusts next months)`;
            }
        }

        if (!dueDateField.value) setNextDueDate(join);
    }

    boardSelect.addEventListener('change', function () {
        filterClassesByBoard(boardSelect.value);
        filterStudents(boardSelect.value, classSelect.value);
    });

    classSelect.addEventListener('change', function () {
        filterStudents(boardSelect.value, classSelect.value);
    });

    studentSelect.addEventListener('change', function () {
        amountField.value = ''; // reset default on manual change
        dueDateField.value = '';
        updateFromStudent();
    });

    submitBtn.addEventListener('click', function () {
        if (!confirm('Are you sure you want to save this fee entry?')) return;

        const opt = studentSelect.options[studentSelect.selectedIndex];
        const fixedFee = parseFloat(opt?.getAttribute('data-fee') || '0') || 0;
        const entered  = parseFloat(amountField.value || '0') || 0;

        if (fixedFee > 0 && Math.abs(entered - fixedFee) > 0.01) {
            if (!confirm(`Entered amount ₹${entered} differs from fixed fee ₹${fixedFee}. Proceed?`)) return;
        }

        document.getElementById('feeForm').submit();
    });

    // ✅ Initial filter + auto-select works when coming from ?student_id=xx
filterClassesByBoard(boardSelect.value);

// If board is selected but class is empty, try set class from selected student
const selectedStudentOpt = studentSelect.options[studentSelect.selectedIndex];
if (selectedStudentOpt && selectedStudentOpt.value) {
    const studentClass = selectedStudentOpt.getAttribute('data-class');

    if (!classSelect.value && studentClass) {
        classSelect.value = studentClass;
    }

    // also ensure board matches selected student (safety)
    const studentBoard = selectedStudentOpt.getAttribute('data-board');
    if (!boardSelect.value && studentBoard) {
        boardSelect.value = studentBoard;
    }
}

// after setting class, filter again
filterClassesByBoard(boardSelect.value);
filterStudents(boardSelect.value, classSelect.value);

updateFromStudent();
})();
</script>
@endpush