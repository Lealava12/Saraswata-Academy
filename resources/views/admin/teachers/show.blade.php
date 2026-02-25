@extends('admin.layouts.app')
@section('title', 'Teacher Details')
@section('page-title', 'Teacher Details')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge me-2"></i>Teacher Details</span>
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>

    <div class="card-body">

        {{-- Teacher Basic Info --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <strong>Name:</strong>
                <div>{{ $teacher->name }}</div>
            </div>

            <div class="col-md-4">
                <strong>Mobile:</strong>
                <div>{{ $teacher->mobile }}</div>
            </div>

            <div class="col-md-4">
                <strong>Joining Date:</strong>
                <div>{{ \Carbon\Carbon::parse($teacher->joining_date)->format('d M Y') }}</div>
            </div>

            <div class="col-md-6 mt-3">
                <strong>Address:</strong>
                <div>{{ $teacher->address ?? 'N/A' }}</div>
            </div>

            <div class="col-md-6 mt-3">
                <strong>Status:</strong>
                <span class="badge {{ $teacher->is_active ? 'bg-success' : 'bg-danger' }}">
                    {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        {{-- Subjects --}}
        <div class="mb-4">
            <h6 class="fw-bold">Subjects</h6>
            @if($teacher->subjects->count())
                @foreach($teacher->subjects as $subject)
                    <span class="badge bg-primary me-1">{{ $subject->name }}</span>
                @endforeach
            @else
                <div class="text-muted">No subjects assigned</div>
            @endif
        </div>

        {{-- Class-wise Salary Table --}}
        <div>
            <h6 class="fw-bold">Class-wise Salary</h6>

            @if($teacher->classes->count())
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Class</th>
                            <th>Board</th>
                            <th>Monthly Fee</th>
                            <th>Teacher Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teacher->classes as $index => $class)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->board?->name ?? 'N/A' }}</td>
                            <td>₹{{ number_format($class->monthly_fee ?? 0, 2) }}</td>
                            <td class="fw-bold text-success">
                                ₹{{ number_format($class->pivot->amount ?? 0, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="text-muted">No classes assigned</div>
            @endif
        </div>

    </div>
</div>
@endsection