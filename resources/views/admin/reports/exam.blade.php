@extends('admin.layouts.app')
@section('title', 'Exam & Academic Reports')
@section('page-title', 'Exam Performance Analytics')

@section('content')
<!-- Filters -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Class</label>
                <select name="class_id" class="form-select border-0 bg-light">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} {{ $c->board ? ' ('. $c->board->name .')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Subject</label>
                <select name="subject_id" class="form-select border-0 bg-light">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $s)
                    <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-uppercase text-muted">From</label>
                <input type="date" name="from_date" class="form-control border-0 bg-light" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-uppercase text-muted">To</label>
                <input type="date" name="to_date" class="form-control border-0 bg-light" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-accent w-100 shadow-sm"><i class="bi bi-search me-2"></i>Analyze</button>
            </div>
        </form>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-pills mb-4 gap-2 p-1 bg-white rounded shadow-sm" id="reportTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-medium px-4" id="detailed-tab" data-bs-toggle="pill" data-bs-target="#detailed" type="button">Detailed Records</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-medium px-4" id="weekly-tab" data-bs-toggle="pill" data-bs-target="#weekly" type="button">Weekly Report</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-medium px-4" id="student-tab" data-bs-toggle="pill" data-bs-target="#student" type="button">Student Performance</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-medium px-4" id="subject-tab" data-bs-toggle="pill" data-bs-target="#subject" type="button">Subject Summary</button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-medium px-4" id="monthly-tab" data-bs-toggle="pill" data-bs-target="#monthly" type="button">Monthly Academic Summary</button>
    </li>
</ul>

<div class="tab-content" id="reportTabsContent">
    <!-- Detailed Tab -->
    <div class="tab-pane fade show active" id="detailed">
        <div class="d-flex justify-content-end gap-2 mb-3">
            <a href="{{ route('admin.reports.exam-csv', request()->query()) }}" class="btn btn-sm btn-outline-success border-0"><i class="bi bi-file-earmark-spreadsheet me-1"></i>CSV Export</a>
            <a href="{{ route('admin.reports.exam-pdf', request()->query()) }}" class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-file-earmark-pdf me-1"></i>PDF Report</a>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Student</th>
                                <th class="text-center">Marks</th>
                                <th class="text-center">%</th>
                                <th class="pe-4 text-center">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($marks as $m)
                            @php
                                $full = $m->exam->full_marks ?? 0;
                                $pct = $full > 0 ? round(($m->marks_obtained/$full)*100, 1) : 0;
                                $grade = $pct>=90?'A+':($pct>=75?'A':($pct>=60?'B':($pct>=45?'C':'F')));
                            @endphp
                            <tr>
                                <td class="ps-4">{{ Carbon\Carbon::parse($m->exam->exam_date)->format('d M Y') }}</td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ optional($m->exam->classInfo)->name }} {{ optional($m->exam->classInfo->board)->name ? '('.$m->exam->classInfo->board->name.')' : '' }}</span></td>
                                <td class="fw-medium">{{ optional($m->exam->subject)->name }}</td>
                                <td>{{ optional($m->student)->name }}</td>
                                <td class="text-center fw-bold">{{ $m->marks_obtained }} / {{ $full }}</td>
                                <td class="text-center">
                                    <div class="small fw-bold">{{ $pct }}%</div>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar {{ $pct>=75?'bg-success':($pct>=45?'bg-warning':'bg-danger') }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                </td>
                                <td class="pe-4 text-center">
                                    <span class="badge {{ $pct>=75?'bg-success':($pct>=45?'bg-warning text-dark':'bg-danger') }} rounded-pill px-3">{{ $grade }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-5 text-muted">No records found for the selected criteria.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Tab -->
    <div class="tab-pane fade" id="weekly">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-bottom-0"><h6 class="mb-0 fw-bold">Weekly Performance Trend</h6></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light"><tr><th class="ps-4">Week Starting</th><th class="text-center">Exams Count</th><th class="text-center pe-4">Weekly Average %</th></tr></thead>
                                <tbody>
                                    @foreach($weeklySummary as $week => $stat)
                                    <tr>
                                        <td class="ps-4 fw-medium">{{ $week }}</td>
                                        <td class="text-center"><span class="badge bg-secondary rounded-pill px-3">{{ $stat['count'] }}</span></td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex align-items-center justify-content-center gap-3">
                                                <div class="fw-bold">{{ round($stat['avg_pct'], 1) }}%</div>
                                                <div class="flex-grow-1" style="max-width: 100px;">
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-primary" style="width: {{ $stat['avg_pct'] }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-accent text-white">
                    <div class="card-body">
                        <h5 class="fw-light small opacity-75 text-uppercase mb-2">Weekly Summary Note</h5>
                        <p class="mb-0 small">Weekly reports aggregate performance across all subjects and classes held during each week. Use this to identify peak exam periods and overall curriculum intensity.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Performance Tab -->
    <div class="tab-pane fade" id="student">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom-0"><h6 class="mb-0 fw-bold">Student Performance Rankings</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Rank</th>
                                <th>Student Name</th>
                                <th class="text-center">Roll No</th>
                                <th class="text-center">Exams Taken</th>
                                <th class="text-center">GPA / Average %</th>
                                <th class="text-center pe-4">Highest Score %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $rank = 1; @endphp
                            @foreach($studentPerformance as $s)
                            <tr>
                                <td class="ps-4"><span class="badge {{ $rank <= 3 ? 'bg-warning text-dark' : 'bg-light text-muted' }} rounded-circle" style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;">{{ $rank++ }}</span></td>
                                <td class="fw-bold text-dark">{{ $s['name'] }} <small class="text-muted d-block">{{ $s['student_id'] }}</small></td>
                                <td class="text-center">{{ $s['roll_no'] }}</td>
                                <td class="text-center fw-medium">{{ $s['exams_taken'] }}</td>
                                <td class="text-center text-primary fw-bold">{{ round($s['avg_pct'], 1) }}%</td>
                                <td class="text-center pe-4 text-success fw-bold">{{ round($s['highest_pct'], 1) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Summary Tab -->
    <div class="tab-pane fade" id="subject">
        <div class="row g-4">
            @foreach($subjectStats as $name => $stat)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h6 class="fw-bold mb-0">{{ $stat['name'] }}</h6>
                            <span class="badge bg-light text-dark fw-light">{{ $stat['total_exams'] }} Exams</span>
                        </div>
                        <div class="display-6 fw-bold mb-2 {{ $stat['avg_pct'] >= 60 ? 'text-success' : 'text-danger' }}">{{ round($stat['avg_pct'] ,1) }}%</div>
                        <p class="text-muted small mb-3">Class Performance Average</p>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar {{ $stat['avg_pct'] >= 60 ? 'bg-success' : 'bg-warning' }}" style="width: {{ $stat['avg_pct'] }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Pass Count:</span>
                            <span class="fw-bold text-success">{{ $stat['pass_count'] }} Students</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Monthly Summary Tab -->
    <div class="tab-pane fade" id="monthly">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom-0"><h6 class="mb-0 fw-bold">Monthly Academic Summary</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-center"><tr><th class="ps-4 text-start">Month</th><th>Records Analyzed</th><th>Performance Yield</th><th class="pe-4">Trend Status</th></tr></thead>
                        <tbody class="text-center">
                            @foreach($monthlySummary as $month => $data)
                            <tr>
                                <td class="text-start ps-4 fw-bold text-dark">{{ $month }}</td>
                                <td>{{ $data['total_entries'] }}</td>
                                <td class="fw-bold text-primary">{{ round($data['avg_pct'], 1) }}%</td>
                                <td class="pe-4">
                                    <span class="badge bg-success-subtle text-success px-3 rounded-pill"><i class="bi bi-arrow-up-right me-1"></i> Stable yield</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link { color: #6c757d; border-radius: 0.5rem; }
    .nav-pills .nav-link:hover { background-color: #f8f9fa; }
    .nav-pills .nav-link.active { background-color: var(--bs-primary-bg-subtle) !important; color: var(--bs-primary) !important; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .progress { background-color: #e9ecef; border-radius: 1rem; overflow: hidden; }
    .btn-outline-success:hover, .btn-outline-danger:hover { background-color: transparent !important; color: inherit !important; transform: translateY(-1px); }
</style>
@endsection
