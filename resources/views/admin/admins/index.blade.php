@extends('admin.layouts.app')
@section('title', 'Admin Accounts')
@section('page-title', 'Admin Accounts')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-shield-lock-fill me-2"></i>Admin Accounts</span>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Admin</a>
    </div>
    <div class="card-body">
        <table class="table data-table table-hover w-100">
            <thead class="table-light"><tr><th>#</th><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($admins as $i => $a)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $a->name }}</td>
                    <td>{{ $a->email }}</td>
                    <td><span class="badge {{ $a->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">{{ $a->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.admins.edit', $a->id) }}" class="btn btn-sm btn-outline-primary" {{ auth('admin')->id() === $a->id ? 'disabled' : '' }}><i class="bi bi-pencil"></i></a>
                            <!-- @if(auth('admin')->id() !== $a->id)
                            <form method="POST" action="{{ route('admin.admins.destroy', $a->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif -->
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
