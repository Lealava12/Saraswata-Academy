@extends('admin.layouts.app')
@section('title', 'Boards')
@section('page-title', 'Boards')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-grid-fill me-2"></i>Boards</span>
        <a href="{{ route('admin.boards.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Board</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light"><tr><th>#</th><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($boards as $i => $b)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $b->name }}</td>
                        <td><span class="badge {{ $b->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $b->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.boards.edit', $b->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.boards.destroy', $b->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
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
