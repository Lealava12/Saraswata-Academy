@extends('admin.layouts.app')
@section('title', 'Study Materials')
@section('page-title', 'Study Materials Library')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-bag-fill me-2"></i>Study Materials</span>
        <a href="{{ route('admin.study-materials.create') }}" class="btn btn-accent btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Material
        </a>
    </div>

    <div class="card-body">
       

        <div class="table-responsive">
            <table class="table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Issued To</th>
                        <th>Status</th>
                        <th width="160">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $i => $m)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $m->name }}</td>
                        <td class="text-muted">{{ \Illuminate\Support\Str::limit($m->description, 60) ?: '-' }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">
                                {{ $m->student_materials_count }} students
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $m->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                {{ $m->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                               @if($m->is_active)
                                <a href="{{ route('admin.study-materials.assign', ['material' => $m->id]) }}"
                                class="btn btn-sm btn-outline-info" title="Assign">
                                    <i class="bi bi-person-check"></i>
                                </a>
                            @else
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Inactive material cannot be assigned">
                                    <i class="bi bi-person-check"></i>
                                </button>
                            @endif

                                <a href="{{ route('admin.study-materials.assignments', ['material' => $m->id]) }}"
                                   class="btn btn-sm btn-outline-success" title="Assignments">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="{{ route('admin.study-materials.edit', $m->id) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form method="POST" action="{{ route('admin.study-materials.destroy', $m->id) }}"
                                      class="d-inline" onsubmit="return confirm('Remove?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No materials found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection