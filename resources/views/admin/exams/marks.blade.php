@extends('admin.layouts.app')
@section('title', 'Exam Marks Entry')
@section('page-title', 'Exam Marks Entry')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-journal-text me-2"></i>
            Marks – {{ $exam->classInfo->name ?? '' }} / {{ $exam->subject->name ?? '' }} – {{ $exam->exam_date }}
            <span class="text-muted small">(Full: {{ $exam->full_marks }})</span>
        </span>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.exams.marks.store', $exam->id) }}">
            @csrf
            <table class="table table-hover">
                <thead class="table-light">
                    <tr><th>#</th><th>Student ID</th><th>Name</th><th>Roll No</th><th style="width:180px">Marks / {{ $exam->full_marks }}</th></tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $s)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td><span class="badge bg-secondary">{{ $s->student_id }}</span></td>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->roll_no }}</td>
                        <td>
                            <input type="number" name="marks[{{ $s->id }}]"
                                class="form-control form-control-sm"
                                min="0" max="{{ $exam->full_marks }}" step="0.5"
                                value="{{ $existingMarks[$s->id]->marks_obtained ?? '' }}"
                                placeholder="—">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-2"></i>Save Marks</button>
        </form>
    </div>
</div>
@endsection
