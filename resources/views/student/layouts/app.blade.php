<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Portal') – Saraswata Academy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --accent: #6366f1; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }
        .navbar-brand .brand-icon { color: var(--accent); }
        .student-nav .nav-link { font-size: .875rem; font-weight: 500; }
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
        .badge-paid    { background: #d1fae5; color: #065f46; }
        .badge-due     { background: #fef3c7; color: #92400e; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }
        .badge-present { background: #d1fae5; color: #065f46; }
        .badge-absent  { background: #fee2e2; color: #991b1b; }
        .stat-card { border-radius: 12px; padding: 1.25rem 1.5rem; }
        footer { background: var(--accent); color: #fff; padding: .75rem 0; font-size: .8rem; }
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm student-nav">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('student.dashboard') }}">
            <i class="bi bi-mortarboard-fill brand-icon fs-4"></i>
            <span class="fw-bold">Saraswata Academy</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#studentNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="studentNav">
            <ul class="navbar-nav me-auto gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('student.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.fees') ? 'active fw-semibold' : '' }}" href="{{ route('student.fees') }}">
                        <i class="bi bi-cash-coin me-1"></i>Fee History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.attendance') ? 'active fw-semibold' : '' }}" href="{{ route('student.attendance') }}">
                        <i class="bi bi-calendar-check me-1"></i>Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.exams') ? 'active fw-semibold' : '' }}" href="{{ route('student.exams') }}">
                        <i class="bi bi-journal-text me-1"></i>Exam Results
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('student.materials') ? 'active fw-semibold' : '' }}" href="{{ route('student.materials') }}">
                        <i class="bi bi-bag me-1"></i>My Materials
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">{{ Auth::guard('student')->user()?->name }}</span>
                <a href="{{ route('student.logout') }}" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @yield('content')
</div>

<footer class="mt-5 text-center">
    <div class="container">© {{ date('Y') }} Saraswata Academy. All rights reserved.</div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
