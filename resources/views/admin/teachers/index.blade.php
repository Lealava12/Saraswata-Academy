@extends('admin.layouts.app')
@section('title', 'Teachers')
@section('page-title', 'Teachers')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge-fill me-2"></i>Teachers</span>
        <a href="{{ route('admin.teachers.create') }}" class="btn btn-accent btn-sm" data-mpin-gate="true"><i
                class="bi bi-plus-lg me-1"></i>Add Teacher</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Subjects</th>
                        <th>Joining Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $i => $t)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $t->name }}</td>
                        <td>{{ $t->mobile }}</td>
                        <td>{{ $t->subjects->pluck('name')->join(', ') ?: '-' }}</td>
                       <td>{{ \Carbon\Carbon::parse($t->joining_date)->format('d M Y') }}</td>
                        <td>
                            <span
                                class="badge {{ $t->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                {{ $t->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.teachers.edit', $t->id) }}"
                                    class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <!-- <form method="POST" action="{{ route('admin.teachers.destroy', $t->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form> -->
                                <a href="{{ route('admin.teachers.show', $t->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
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