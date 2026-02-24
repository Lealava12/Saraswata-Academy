@extends('student.layouts.app')
@section('title', 'My Study Materials')

@section('content')
<h5 class="fw-bold mb-4"><i class="bi bi-bag-fill me-2 text-secondary"></i>My Study Materials</h5>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Material Name</th><th>Description</th><th>Issue Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($materials as $i => $m)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td class="fw-medium">{{ $m->material->name ?? '-' }}</td>
                    <td class="text-muted small">{{ Str::limit($m->material->description ?? '', 80) }}</td>
                    <td>{{ $m->issue_date }}</td>
                    <td>
                        <span class="badge bg-info-subtle text-info">{{ $m->status }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No study materials assigned yet.</td></tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
