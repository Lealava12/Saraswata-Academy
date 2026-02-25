<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – Saraswata Academy Admin</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1a1f2e;
            --sidebar-width: 260px;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --border-radius: 12px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            overflow-x: hidden;
        }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            transition: all .4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            color: rgba(248, 250, 252, 0.8);
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.1);
        }

        /* Custom Scrollbar for Sidebar */
        #sidebar::-webkit-scrollbar {
            width: 5px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 991.98px) {
            #sidebar {
                left: calc(-1 * var(--sidebar-width));
            }

            #sidebar.show {
                left: 0;
            }

            #main {
                margin-left: 0 !important;
            }

            #topbar {
                left: 0 !important;
            }
        }

        .sidebar-brand {
            padding: 1.75rem 1.5rem;
            position: relative;
        }

        .sidebar-brand::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1.5rem;
            right: 1.5rem;
            height: 1px;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.3) 0%, transparent 100%);
        }

        .sidebar-brand h5 {
            color: #fff;
            font-weight: 800;
            margin: 0;
            font-size: 1.1rem;
            letter-spacing: -0.02em;
        }

        .sidebar-brand small {
            color: rgba(148, 163, 184, 0.8);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.25rem;
            display: block;
        }

        .sidebar-nav {
            padding: 1.25rem 0.75rem;
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
        }

        .sidebar-nav .nav-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: rgba(148, 163, 184, 0.45);
            padding: 1.25rem 0.75rem 0.5rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(148, 163, 184, 0.85);
            padding: 0.7rem 1rem;
            border-radius: 10px;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            border: 1px solid transparent;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.03);
            color: #fff;
            transform: translateX(4px);
        }

        .sidebar-nav .nav-link.active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0.05) 100%);
            color: #818cf8;
            border-color: rgba(99, 102, 241, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav .nav-link.active::before {
            content: '';
            position: absolute;
            left: -0.75rem;
            top: 0.6rem;
            bottom: 0.6rem;
            width: 4px;
            background: #6366f1;
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .sidebar-nav .nav-link:hover i {
            transform: scale(1.1);
        }

        /* Topbar */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 60px;
            background: #fff;
            box-shadow: 0 1px 0 rgba(0, 0, 0, .06);
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            transition: all .3s ease;
        }

        #sidebarToggle {
            display: none;
            padding: .25rem .5rem;
            font-size: 1.25rem;
            border: none;
            background: transparent;
            color: #64748b;
        }

        @media (max-width: 991.98px) {
            #sidebarToggle {
                display: block;
            }
        }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Main content */
        #main {
            margin-left: var(--sidebar-width);
            padding-top: 60px;
            min-height: 100vh;
            transition: all .3s ease;
        }

        .page-content {
            padding: 1.75rem;
        }

        @media (max-width: 575.98px) {
            .page-content {
                padding: 1rem;
            }
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            font-weight: 600;
        }

        /* Stat cards */
        .stat-card {
            border-radius: var(--border-radius);
            padding: 1.25rem 1.5rem;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .stat-card .stat-icon {
            font-size: 2rem;
            opacity: .2;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Badges */
        .badge-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-due {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-overdue {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Buttons */
        .btn-accent {
            background: var(--accent);
            color: #fff;
            border: none;
        }

        .btn-accent:hover {
            background: var(--accent-hover);
            color: #fff;
        }

        /* DataTable */
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: .35rem .75rem;
        }

        /* Table Responsiveness */
        .table-responsive {
            border-radius: var(--border-radius);
        }

        /* MPIN Modal */
        #mpinModal .modal-content {
            border-radius: 16px;
        }
    </style>
    @stack('styles')
</head>

<body>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-brand">
            <h5><i class="bi bi-mortarboard-fill me-2" style="color: #6366f1"></i>Saraswata</h5>
            <small>Academy Admin Panel</small>
        </div>
        <div class="sidebar-nav">
            <div class="nav-label">Main</div>
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.admins.index') }}"
                class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock-fill"></i> Admins
            </a>

            <div class="nav-label">Setup</div>
            <a href="{{ route('admin.boards.index') }}"
                class="nav-link {{ request()->routeIs('admin.boards.*') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Boards
            </a>
            <a href="{{ route('admin.classes.index') }}"
                class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                <i class="bi bi-layers-fill"></i> Classes
            </a>
            <a href="{{ route('admin.subjects.index') }}"
                class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                <i class="bi bi-book-fill"></i> Subjects
            </a>

            <div class="nav-label">Academics</div>
            <a href="{{ route('admin.students.index') }}"
                class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Students
            </a>
            <a href="{{ route('admin.teachers.index') }}"
                class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Teachers
            </a>
            <a href="{{ route('admin.staff.index') }}"
                class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Staffs
            </a>
            <a href="{{ route('admin.attendance.index') }}"
                class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i> Attendance
            </a>
            <a href="{{ route('admin.exams.index') }}"
                class="nav-link {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> Exams & Marks
            </a>
            <a href="{{ route('admin.study-materials.index') }}"
                class="nav-link {{ request()->routeIs('admin.study-materials.*') ? 'active' : '' }}">
                <i class="bi bi-bag-fill"></i> Study Material
            </a>

            <div class="nav-label">Finance</div>
            <a href="{{ route('admin.fees.index') }}"
                class="nav-link {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Student Fees
            </a>
            <a href="{{ route('admin.teacher-salary.index') }}"
                class="nav-link {{ request()->routeIs('admin.teacher-salary.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Teacher Salary
            </a>
            <a href="{{ route('admin.staff-salary.index') }}"
                class="nav-link {{ request()->routeIs('admin.staff-salary.*') ? 'active' : '' }}">
                <i class="bi bi-wallet-fill"></i> Staff Salary
            </a>
            <a href="{{ route('admin.expenditures.index') }}"
                class="nav-link {{ request()->routeIs('admin.expenditures.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Expenditures
            </a>

            <div class="nav-label">Reports</div>
            <a href="{{ route('admin.reports.attendance') }}"
                class="nav-link {{ request()->routeIs('admin.reports.attendance') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> Attendance
            </a>
            
            <a href="{{ route('admin.reports.exam') }}"
                class="nav-link {{ request()->routeIs('admin.reports.exam') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> Exam
            </a>
            <a href="{{ route('admin.reports.fee') }}"
                class="nav-link {{ request()->routeIs('admin.reports.fee') ? 'active' : '' }}">
                <i class="bi bi-currency-rupee"></i> Fee
            </a>
            <a href="{{ route('admin.reports.financial') }}"
                class="nav-link {{ request()->routeIs('admin.reports.financial') ? 'active' : '' }}">
                <i class="bi bi-pie-chart-fill"></i> Financial
            </a>


        </div>
    </nav>

    <!-- Topbar -->
    <div id="topbar">
        <button id="sidebarToggle"><i class="bi bi-list"></i></button>
        <span class="text-muted fw-medium flex-grow-1">@yield('page-title', 'Dashboard')</span>
        <div class="dropdown">
            <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1"></i>{{ Auth::guard('admin')->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i
                            class="bi bi-person me-2"></i>Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="{{ route('admin.logout') }}"><i
                            class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main">
        <div class="page-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>

    <!-- MPIN Modal -->
    <div class="modal fade" id="mpinModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold"><i class="bi bi-lock-fill text-warning me-2"></i>Enter MPIN</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="password" id="mpinInput" class="form-control text-center fs-4 letter-spacing-3"
                        maxlength="6" placeholder="••••••">
                    <div id="mpinError" class="text-danger small mt-2 d-none">Invalid MPIN. Try again.</div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-accent w-100" id="mpinConfirmBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Auto-init DataTables on all .data-table elements
        $(document).ready(function() {
            $('.data-table').DataTable({
                responsive: true,
                pageLength: 25,
                language: {
                    search: '',
                    searchPlaceholder: 'Search...',
                    emptyTable: 'No records found'
                }
            });

            // Sidebar Toggle Control
            $('#sidebarToggle, #sidebarOverlay').on('click', function() {
                $('#sidebar').toggleClass('show');
                $('#sidebarOverlay').toggleClass('show');
                $('body').toggleClass('overflow-hidden');
            });
        });
        // Global MPIN Gate Logic
        $(document).on('click', '[data-mpin-gate="true"]', function(e) {
            e.preventDefault();
            const targetUrl = $(this).attr('href');
            const verifyUrl = '{{ route("admin.teacher-salary.verify-mpin") }}'; // Using existing global verifier

            const modal = new bootstrap.Modal(document.getElementById('mpinModal'));
            modal.show();

            $('#mpinInput').val('');
            $('#mpinError').addClass('d-none');

            $('#mpinConfirmBtn').off('click').on('click', function() {
                const mpin = $('#mpinInput').val();
                $.post(verifyUrl, { mpin: mpin }, function(res) {
                    if (res.success) {
                        modal.hide();
                        window.location.href = targetUrl;
                    } else {
                        $('#mpinError').removeClass('d-none').text(res.message);
                    }
                });
            });
        });

        // CSRF for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
