@extends('admin.layouts.app')
@section('title', 'Expenditures')
@section('page-title', 'Expenditure Records')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2"></i>Expenditures</span>
        <a href="{{ route('admin.expenditures.create') }}" class="btn btn-accent btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Expenditure</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light">
                    <tr><th>#</th><th>Title</th><th>Amount</th><th>Date</th><th>Description</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($expenditures as $i => $e)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $e->title }}</td>
                        <td class="fw-semibold text-danger">₹{{ number_format($e->amount) }}</td>
                        <td>{{ $e->expense_date }}</td>
                        <td>{{ Str::limit($e->description, 50) ?: '-' }}</td>
                        <td>
                            <span class="badge {{ $e->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                {{ $e->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.expenditures.edit', $e->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.expenditures.destroy', $e->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
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
