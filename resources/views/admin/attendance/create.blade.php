@extends('admin.layouts.app')
@section('title', 'Attendance')
@section('page-title', 'Mark Attendance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-plus me-2"></i>Mark Attendance</span>
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.attendance.store') }}" id="attendanceForm">
            @csrf
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Class <span class="text-danger">*</span></label>
                    <select name="class_id" id="classSelect" class="form-select" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                    <input type="date" name="attendance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-medium">Subjects <span class="text-danger">*</span></label>
                <div id="subjectsContainer">
                    <div class="row g-2 mb-2 subject-row">
                        <div class="col-md-6">
                            <select name="subjects[]" class="form-select subject-select" required>
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-success btn-sm add-more-subject">
                                <i class="bi bi-plus-circle"></i> Add More
                            </button>
                        </div>
                    </div>
                </div>
                <small class="text-muted">Add multiple subjects for the same attendance</small>
                <div id="subjectError" class="text-danger mt-2" style="display: none;"></div>
            </div>

            <div class="mb-3">
                <button type="button" class="btn btn-accent" id="loadStudentsBtn" disabled>
                    <i class="bi bi-search me-1"></i>Load Students
                </button>
            </div>

            <div id="studentsContainer" class="d-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Student List</h6>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-success btn-sm" id="markAllPresent">
                            <i class="bi bi-check-all"></i> Mark All Present
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="markAllAbsent">
                            <i class="bi bi-x-lg"></i> Mark All Absent
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">Student ID</th>
                                <th width="25%">Name</th>
                                <th width="10%">Roll No</th>
                                <th width="45%">Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody id="studentsList">
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    Select class and subjects, then click "Load Students"
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-accent px-4">
                        <i class="bi bi-check-lg me-2"></i>Save Attendance
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Function to check for duplicate subjects
    function checkDuplicateSubjects() {
        const subjectValues = [];
        let hasDuplicate = false;
        
        $('select[name="subjects[]"]').each(function() {
            const value = $(this).val();
            if (value && value !== '') {
                if (subjectValues.includes(value)) {
                    hasDuplicate = true;
                    $(this).addClass('is-invalid');
                } else {
                    subjectValues.push(value);
                    $(this).removeClass('is-invalid');
                }
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (hasDuplicate) {
            $('#subjectError').text('Duplicate subjects selected. Please choose different subjects.').show();
        } else {
            $('#subjectError').hide();
        }
        
        return !hasDuplicate;
    }

    // Enable/disable load students button based on selections
    function validateLoadButton() {
        const classSelected = $('#classSelect').val();
        const subjectsSelected = $('select[name="subjects[]"]').filter(function() {
            return $(this).val() !== '';
        }).length > 0;
        const noDuplicates = checkDuplicateSubjects();
        
        $('#loadStudentsBtn').prop('disabled', !(classSelected && subjectsSelected && noDuplicates));
    }

    // Check on class change
    $('#classSelect').on('change', validateLoadButton);

    // Check on subject changes
    $(document).on('change', 'select[name="subjects[]"]', function() {
        validateLoadButton();
    });

    // Add more subject button
    $(document).on('click', '.add-more-subject', function() {
        const subjectOptions = `@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach`;
        
        const newRow = `
            <div class="row g-2 mb-2 subject-row">
                <div class="col-md-6">
                    <select name="subjects[]" class="form-select subject-select">
                        <option value="">-- Select Subject --</option>
                        ${subjectOptions}
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-subject">
                        <i class="bi bi-dash-circle"></i> Remove
                    </button>
                </div>
            </div>
        `;
        $('#subjectsContainer').append(newRow);
        validateLoadButton();
    });

    // Remove subject row
    $(document).on('click', '.remove-subject', function() {
        if ($('.subject-row').length > 1) {
            $(this).closest('.subject-row').remove();
            validateLoadButton();
        } else {
            alert('At least one subject is required.');
        }
    });

    // Load students
    $('#loadStudentsBtn').on('click', function() {
        const classId = $('#classSelect').val();
        if (!classId) {
            alert('Please select a class.');
            return;
        }

        // Check for duplicates one more time before loading
        if (!checkDuplicateSubjects()) {
            alert('Please fix duplicate subjects before loading students.');
            return;
        }

        // Show loading state
        $('#studentsList').html('<tr><td colspan="5" class="text-center">Loading students...</td></tr>');
        
        $.ajax({
            url: '{{ route("admin.attendance.get-students") }}',
            type: 'POST',
            data: {
                class_id: classId,
                _token: '{{ csrf_token() }}'
            },
            success: function(students) {
                if (students.length === 0) {
                    $('#studentsList').html('<tr><td colspan="5" class="text-center text-warning">No students found in this class.</td></tr>');
                } else {
                    let rows = '';
                    students.forEach((student, index) => {
                        rows += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td><span class="badge bg-secondary">${student.student_id}</span></td>
                                <td>${student.name}</td>
                                <td class="text-center">${student.roll_no || '-'}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check attendance-radio" 
                                            name="attendance[${student.id}]" value="Present" 
                                            id="present_${student.id}" checked>
                                        <label class="btn btn-outline-success btn-sm" 
                                            for="present_${student.id}">
                                            <i class="bi bi-check-circle"></i> Present
                                        </label>
                                        
                                        <input type="radio" class="btn-check attendance-radio" 
                                            name="attendance[${student.id}]" value="Absent" 
                                            id="absent_${student.id}">
                                        <label class="btn btn-outline-danger btn-sm" 
                                            for="absent_${student.id}">
                                            <i class="bi bi-x-circle"></i> Absent
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    $('#studentsList').html(rows);
                }
                $('#studentsContainer').removeClass('d-none');
            },
            error: function() {
                $('#studentsList').html('<tr><td colspan="5" class="text-center text-danger">Error loading students. Please try again.</td></tr>');
            }
        });
    });

    // Mark all present
    $('#markAllPresent').on('click', function() {
        $('.attendance-radio[value="Present"]').prop('checked', true);
    });

    // Mark all absent
    $('#markAllAbsent').on('click', function() {
        $('.attendance-radio[value="Absent"]').prop('checked', true);
    });

    // Form validation before submit
    $('#attendanceForm').on('submit', function(e) {
        const subjectsSelected = $('select[name="subjects[]"]').filter(function() {
            return $(this).val() !== '';
        }).length;
        
        if (subjectsSelected === 0) {
            e.preventDefault();
            alert('Please select at least one subject.');
            return false;
        }
        
        if (!checkDuplicateSubjects()) {
            e.preventDefault();
            alert('Please fix duplicate subjects before saving.');
            return false;
        }
        
        if ($('.attendance-radio').length === 0) {
            e.preventDefault();
            alert('Please load students first.');
            return false;
        }
    });

    // Also add server-side validation in the controller
});
</script>
@endpush