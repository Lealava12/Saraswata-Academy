@extends('admin.layouts.app')
@section('title', 'Study Materials')
@section('page-title', 'Study Materials Library')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-bag-fill me-2"></i>Study Materials</span>
        <a href="{{ route('admin.study-materials.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Material</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light"><tr><th>#</th><th>Name</th><th>Description</th><th>Issued To</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($materials as $i => $m)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $m->name }}</td>
                        <td class="text-muted">{{ Str::limit($m->description, 60) ?: '-' }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">
                                {{ $m->studentMaterials->count() }} students
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $m->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                {{ $m->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.study-materials.assign', $m->id) }}" class="btn btn-sm btn-outline-info" title="Assign"><i class="bi bi-person-check"></i></a>
                                <a href="{{ route('admin.study-materials.edit', $m->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.study-materials.destroy', $m->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
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
