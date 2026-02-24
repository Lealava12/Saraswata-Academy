@extends('admin.layouts.app')
@section('title', 'Students')
@section('page-title', 'Students')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people-fill me-2"></i>All Students</span>
        <a href="{{ route('admin.students.create') }}" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Student
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Class</th>
                        <th>Board</th>
                        <th>Roll No</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $s)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td><span class="badge bg-secondary">{{ $s->student_id }}</span></td>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->mobile }}</td>
                        <td>{{ $s->classInfo->name ?? '-' }}</td>
                        <td>{{ $s->board->name ?? '-' }}</td>
                        <td>{{ $s->roll_no }}</td>
                        <td>
                            @if($s->is_active)
                                <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.students.show', $s->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $s->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('admin.students.fee-history', $s->id) }}" class="btn btn-sm btn-outline-success" title="Fee History">
                                    <i class="bi bi-cash-coin"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.students.destroy', $s->id) }}" class="d-inline" onsubmit="return confirm('Remove this student?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Remove"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
