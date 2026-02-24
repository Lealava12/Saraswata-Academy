@extends('admin.layouts.app')
@section('title', 'Subjects')
@section('page-title', 'Subjects')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-book-fill me-2"></i>Subjects</span>
        <a href="{{ route('admin.subjects.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Subject</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light"><tr><th>#</th><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($subjects as $i => $s)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $s->name }}</td>
                        <td><span class="badge {{ $s->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.subjects.edit', $s->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.subjects.destroy', $s->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
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
