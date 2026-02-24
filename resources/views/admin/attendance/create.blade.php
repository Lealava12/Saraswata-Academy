@extends('admin.layouts.app')
@section('title', 'Attendance')
@section('page-title', 'Mark Attendance')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-plus me-2"></i>Mark Attendance</span>
        <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.attendance.store') }}" id="attendanceForm">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Class <span class="text-danger">*</span></label>
                    <select name="class_id" id="classSelect" class="form-select" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Subject(s) <span class="text-danger">*</span></label>
                    <select name="subjects[]" class="form-select" multiple required>
                        @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Ctrl+Click to select multiple</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Date <span class="text-danger">*</span></label>
                    <input type="date" name="attendance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-accent btn-sm" id="loadStudentsBtn">
                        <i class="bi bi-search me-1"></i>Load Students
                    </button>
                </div>
            </div>

            <div id="studentsContainer" class="d-none">
                <div class="table-responsive">
                    <table class="table table-bordered mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Roll No</th>
                                <th class="text-center">
                                    <div class="d-flex gap-3 justify-content-center">
                                        <label><input type="radio" name="mark_all" value="Present" id="markAllPresent"> Mark All Present</label>
                                        <label><input type="radio" name="mark_all" value="Absent" id="markAllAbsent"> Mark All Absent</label>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="studentsList"></tbody>
                    </table>
                </div>
                <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Save Attendance</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#loadStudentsBtn').on('click', function() {
    const classId = $('#classSelect').val();
    if (!classId) return alert('Please select a class first.');

    $.post('{{ route("admin.attendance.get-students") }}', {class_id: classId}, function(students) {
        let rows = '';
        students.forEach((s, i) => {
            rows += `<tr>
                <td>${i+1}</td>
                <td><span class="badge bg-secondary">${s.student_id}</span></td>
                <td>${s.name}</td>
                <td>${s.roll_no}</td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check attendance-radio" name="attendance[${s.id}]" value="Present" id="p${s.id}" required>
                        <label class="btn btn-outline-success btn-sm" for="p${s.id}"><i class="bi bi-check-lg"></i> Present</label>
                        <input type="radio" class="btn-check attendance-radio" name="attendance[${s.id}]" value="Absent" id="a${s.id}">
                        <label class="btn btn-outline-danger btn-sm" for="a${s.id}"><i class="bi bi-x-lg"></i> Absent</label>
                    </div>
                </td>
            </tr>`;
        });
        $('#studentsList').html(rows);
        $('#studentsContainer').removeClass('d-none');
    });
});

// Mark all buttons
$('#markAllPresent').on('change', function() {
    if (this.checked) $('input[value="Present"].attendance-radio').prop('checked', true);
});
$('#markAllAbsent').on('change', function() {
    if (this.checked) $('input[value="Absent"].attendance-radio').prop('checked', true);
});
</script>
@endpush
