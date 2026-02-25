@extends('admin.layouts.app')
@section('title', 'Staff')
@section('page-title', 'Non-Teaching Staff')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-gear me-2"></i>Non-Teaching Staff</span>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-accent btn-sm" data-mpin-gate="true"><i class="bi bi-plus-lg me-1"></i>Add Staff</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light">
                    <tr><th>#</th><th>Name</th><th>Role</th><th>Mobile</th><th>Monthly Salary</th><th>Joining Date</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($staff as $i => $s)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->role }}</td>
                        <td>{{ $s->mobile }}</td>
                        <td>₹{{ number_format($s->monthly_salary) }}</td>
                        <td>{{ $s->joining_date }}</td>
                        <td><span class="badge {{ $s->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.staff.edit', $s->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <!-- <form method="POST" action="{{ route('admin.staff.destroy', $s->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
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
