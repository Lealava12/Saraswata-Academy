@extends('admin.layouts.app')
@section('title', 'Exams')
@section('page-title', 'Exams')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-text me-2"></i>Exams</span>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Exam</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light">
                    <tr><th>#</th><th>Class</th><th>Subject</th><th>Date</th><th>Full Marks</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($exams as $i => $exam)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $exam->classInfo->name ?? '-' }}</td>
                        <td>{{ $exam->subject->name ?? '-' }}</td>
                        <td>{{ $exam->exam_date }}</td>
                        <td>{{ $exam->full_marks }}</td>
                        <td>
                            <span class="badge {{ $exam->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                {{ $exam->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.exams.marks', $exam->id) }}" class="btn btn-sm btn-outline-success" title="Enter Marks"><i class="bi bi-pencil-square"></i></a>
                                <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.exams.destroy', $exam->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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
