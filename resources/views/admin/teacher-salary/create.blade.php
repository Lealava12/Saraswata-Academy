@extends('admin.layouts.app')
@section('title', 'Pay Teacher Salary')
@section('page-title', 'Pay Teacher Salary')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-wallet2 me-2"></i>Pay Teacher Salary</span>
        <a href="{{ route('admin.teacher-salary.index') }}" class="btn btn-sm btn-outline-secondary"><i
                class="bi bi-arrow-left me-1"></i>Back</a>
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

                <!-- Dynamic Classes Container -->
                <div class="col-12" id="classesContainer" style="display: none;">
                    <div class="card bg-light mt-3 border mb-3">
                        <div class="card-header fw-bold">Assigned Classes <span id="teacherNameDisplay"></span></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Class</th>
                                            <th>Fee per Class (₹)</th>
                                            <th>No. of Classes</th>
                                            <th>Total (₹)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="classesTbody">
                                        <!-- Dynamic rows will go here -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary fw-bold">
                                            <td colspan="2" class="text-end">Grand Total:</td>
                                            <td id="grandTotalClasses">0</td>
                                            <td id="grandTotalAmount">₹0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3" style="display: none;">
                    <label class="form-label fw-medium">Total No. of Classes</label>
                    <input type="number" id="classCount" name="class_count" class="form-control" min="0" value="0" readonly>
                </div>

                <div class="col-md-3" style="display: none;">
                    <label class="form-label fw-medium">Total Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" id="amount" name="amount" class="form-control" step="0.01" value="0" readonly required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Payment Month <span class="text-danger">*</span></label>
                    <input type="month" name="payment_month" class="form-control" value="{{ date('Y-m') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-medium">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <!-- Hidden Breakdown Inputs Container -->
                <div id="breakdownInputs"></div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-accent px-4">
                        <i class="bi bi-wallet2 me-2"></i>Pay Salary
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
const teachersData = @json($teachers);
const teacherSel = document.getElementById('teacherSel');
const classesContainer = document.getElementById('classesContainer');
const classesTbody = document.getElementById('classesTbody');
const grandTotalClasses = document.getElementById('grandTotalClasses');
const grandTotalAmount = document.getElementById('grandTotalAmount');
const amountInput = document.getElementById('amount');
const classCountInput = document.getElementById('classCount');
const teacherNameDisplay = document.getElementById('teacherNameDisplay');

teacherSel.addEventListener('change', function() {
    const teacherId = this.value;
    classesTbody.innerHTML = '';
    
    if (!teacherId) {
        classesContainer.style.display = 'none';
        updateTotals();
        return;
    }

    const teacher = teachersData.find(t => t.id == teacherId);
    if (teacher && teacher.classes && teacher.classes.length > 0) {
        teacherNameDisplay.textContent = `(${teacher.name})`;
        classesContainer.style.display = 'block';
        
        teacher.classes.forEach((cls) => {
            const fee = parseFloat(cls.pivot.amount) || 0;
            const boardName = cls.board?.name ? `(${cls.board.name})` : '';
            const tr = document.createElement('tr');
            
            tr.setAttribute('data-class-id', cls.id);
            tr.innerHTML = `
                <td class="align-middle">${cls.name} ${boardName}</td>
                <td class="align-middle">₹${fee.toFixed(2)}</td>
                <td style="width: 150px;">
                    <input type="number" class="form-control form-control-sm class-count-input" 
                        data-fee="${fee}" data-class-name="${cls.name} ${boardName}" min="0" value="0">
                </td>
                <td class="align-middle fw-semibold row-total">₹0.00</td>
            `;
            classesTbody.appendChild(tr);
        });

        // Add event listeners to new inputs
        document.querySelectorAll('.class-count-input').forEach(input => {
            input.addEventListener('input', function() {
                const count = parseInt(this.value) || 0;
                if (count < 0) this.value = 0;
                const fee = parseFloat(this.getAttribute('data-fee')) || 0;
                const total = Math.max(0, count) * fee;
                this.closest('tr').querySelector('.row-total').textContent = `₹${total.toFixed(2)}`;
                updateTotals();
            });
        });
    } else {
        classesContainer.style.display = 'none';
        // You can uncomment below if you want an empty state visible. 
        // classesContainer.style.display = 'block';
        // classesTbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">No classes assigned with fees.</td></tr>`;
    }
    
    updateTotals();
});

function updateTotals() {
    let totalClasses = 0;
    let totalAmt = 0;
    
    document.querySelectorAll('.class-count-input').forEach(input => {
        const count = parseInt(input.value) || 0;
        const fee = parseFloat(input.getAttribute('data-fee')) || 0;
        if (count > 0) {
            totalClasses += count;
            totalAmt += (count * fee);
        }
    });
    
    grandTotalClasses.textContent = totalClasses;
    grandTotalAmount.textContent = `₹${totalAmt.toFixed(2)}`;
    
    classCountInput.value = totalClasses;
    amountInput.value = totalAmt.toFixed(2);

    // Update Hidden Breakdown Inputs
    const breakdownInputs = document.getElementById('breakdownInputs');
    breakdownInputs.innerHTML = '';
    let idx = 0;
    document.querySelectorAll('.class-count-input').forEach(input => {
        const count = parseInt(input.value) || 0;
        if (count > 0) {
            const classId = input.closest('tr').getAttribute('data-class-id');
            const className = input.getAttribute('data-class-name');
            const fee = input.getAttribute('data-fee');
            
            breakdownInputs.innerHTML += `
                <input type="hidden" name="breakdown[${idx}][class_name]" value="${className}">
                <input type="hidden" name="breakdown[${idx}][count]" value="${count}">
                <input type="hidden" name="breakdown[${idx}][fee]" value="${fee}">
                <input type="hidden" name="breakdown[${idx}][total]" value="${(count * parseFloat(fee)).toFixed(2)}">
            `;
            idx++;
        }
    });
}

const tSalForm = document.getElementById('tSalForm');

tSalForm.addEventListener('submit', function(e) {
    const teacherText = teacherSel.options[teacherSel.selectedIndex]?.text ?? '';
    const amt = amountInput.value || '0';
    const month = document.querySelector('input[name="payment_month"]').value || '';
    const payDate = document.querySelector('input[name="payment_date"]').value || '';
    const count = classCountInput.value || '0';

    if (parseFloat(amt) <= 0) {
        alert('Total amount must be greater than 0. Please enter the number of classes.');
        e.preventDefault();
        return;
    }

    let classBreakdown = '';
    document.querySelectorAll('.class-count-input').forEach(input => {
        const c = parseInt(input.value) || 0;
        if (c > 0) {
            const cName = input.getAttribute('data-class-name');
            const fee = parseFloat(input.getAttribute('data-fee')) || 0;
            const rTotal = c * fee;
            classBreakdown += `  - ${cName}: ${c} classes (₹${rTotal.toFixed(2)})\n`;
        }
    });

    const msg =
        `Confirm Salary Payment?

Teacher: ${teacherText}
Month: ${month}
Payment Date: ${payDate}

Total Classes: ${count}
Total Amount: ₹${amt}

Class Breakdown:
${classBreakdown || '  (None)'}

Press OK to submit, Cancel to stop.`;

    if (!confirm(msg)) {
        e.preventDefault(); // stop submission
    }
});
</script>
@endpush