@extends('admin.layouts.app')
@section('title', 'Add Student')
@section('page-title', 'Add New Student')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-plus-fill me-2"></i>Add New Student</span>
        <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.students.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <!-- Auto-generated Student ID -->
                <div class="col-md-4">
                    <label class="form-label fw-medium">Student ID <span class="badge bg-secondary">Auto</span></label>
                    <input type="text" class="form-control bg-light" value="{{ $nextId }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div> 
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mobile <span class="text-danger">*</span></label>
                    <input type="number" id="mobile" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
                 </div> 
                <div class="col-md-4">
                    <label class="form-label fw-medium">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Board <span class="text-danger">*</span></label>
                    <select name="board_id" class="form-select @error('board_id') is-invalid @enderror" required>
                        <option value="">-- Select Board --</option>
                        @foreach($boards as $b)
                        <option value="{{ $b->id }}" {{ old('board_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Class <span class="text-danger">*</span></label>
                    <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" data-board="{{ $c->board_id }}" data-fee="{{ $c->monthly_fee }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->board->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Monthly Fees <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="monthly_fees" id="monthly_fees" class="form-control @error('monthly_fees') is-invalid @enderror" value="{{ old('monthly_fees', 0) }}" required >
                    @error('monthly_fees')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ old('joining_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-medium">School Name</label>
                    <input type="text" name="school_name" class="form-control" value="{{ old('school_name') }}" placeholder="Student's school (optional)">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Progress Report</label>
                    <input type="file" name="progress_report" class="form-control" accept=".pdf,image/*">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12"><hr class="my-1"><h6 class="text-muted fw-semibold">Parent / Guardian Details</h6></div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Father's Name</label>
                    <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Father's Mobile</label>
              <input type="number" id="father_mobile" name="father_mobile" class="form-control" value="{{ old('father_mobile') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mother's Name</label>
                    <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Mother's Mobile</label>
                 <input type="number" id="mother_mobile" name="mother_mobile" class="form-control" value="{{ old('mother_mobile') }}">                </div>
                <div class="col-md-8">
                    <label class="form-label fw-medium">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                </div>

                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Save
                            Student</button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-light ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", function () {

    function limitMobileInput(element) {
        element.addEventListener("input", function () {
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });
    }

    limitMobileInput(document.getElementById("mobile"));
    limitMobileInput(document.getElementById("father_mobile"));
    limitMobileInput(document.getElementById("mother_mobile"));

    // Dynamic class filtering and fee assignment
    const boardSelect = document.querySelector('select[name="board_id"]');
    const classSelect = document.getElementById('class_id');
    const classOptions = Array.from(classSelect.querySelectorAll('option'));
    const monthlyFeesInput = document.getElementById('monthly_fees');

    function filterClasses() {
        const selectedBoardId = boardSelect.value;
        let firstValidOption = null;

        classOptions.forEach(option => {
            if (option.value === "") {
                option.style.display = 'block';
                return;
            }

            if (!selectedBoardId || option.dataset.board === selectedBoardId) {
                option.style.display = 'block';
                if(!firstValidOption) firstValidOption = option;
            } else {
                option.style.display = 'none';
                if (classSelect.value === option.value) {
                    classSelect.value = '';
                }
            }
        });
        
        updateFees();
    }

    function updateFees() {
        const selectedOption = classSelect.options[classSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.fee) {
            monthlyFeesInput.value = selectedOption.dataset.fee;
        } else {
            monthlyFeesInput.value = 0;
        }
    }

    boardSelect.addEventListener('change', filterClasses);
    classSelect.addEventListener('change', updateFees);

    // Initial run in case of old inputs
    filterClasses();
    updateFees();

});
</script>
@endsection
