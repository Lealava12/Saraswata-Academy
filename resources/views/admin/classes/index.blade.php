@extends('admin.layouts.app')
@section('title', 'Classes')
@section('page-title', 'Classes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-layers-fill me-2"></i>Classes</span>
        <a href="{{ route('admin.classes.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Class</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light"><tr><th>#</th><th>Name</th><th>Board</th><th>Monthly Fee</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($classes as $i => $c)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->board->name ?? '-' }}</td>
                        <td class="fw-semibold">₹{{ number_format($c->monthly_fee) }}</td>
                        <td><span class="badge {{ $c->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $c->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.classes.edit', $c->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <!-- <form method="POST" action="{{ route('admin.classes.destroy', $c->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form> -->
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
