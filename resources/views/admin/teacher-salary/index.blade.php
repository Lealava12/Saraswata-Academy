@extends('admin.layouts.app')
@section('title', 'Teacher Salary')
@section('page-title', 'Teacher Salary Records')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-wallet2 me-2"></i>Teacher Salary Records</span>
        <a href="{{ route('admin.teacher-salary.create') }}" class="btn btn-accent btn-sm" data-mpin-gate="true"><i class="bi bi-plus-lg me-1"></i>Pay Salary</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table data-table table-hover w-100">
                <thead class="table-light">
                    <tr><th>#</th><th>Teacher</th><th>Class</th><th>Month</th><th>Amount</th><th>Payment Date</th><th>Classes</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @foreach($salaries as $i => $s)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $s->teacher->name ?? '-' }}</td>
                        <td>{{ $s->class->name ?? '-' }}</td>
                        <td>{{ $s->payment_month }}</td>
                        <td class="fw-semibold">₹{{ number_format($s->amount) }}</td>
                        <td>{{ $s->payment_date }}</td>
                        <td>{{ $s->class_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.teacher-salary.destroy', $s->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
