@extends('admin.layouts.app')
@section('title', 'Financial Report')
@section('page-title', 'Financial Report')

@section('content')
<!-- Year Selector -->
<div class="d-flex align-items-center gap-3 mb-4">
    <form method="GET" class="d-flex gap-2 align-items-center">
        <label class="fw-medium">Year:</label>
        <select name="year" class="form-select form-select-sm" style="width:120px" onchange="this.form.submit()">
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card text-white" style="background:linear-gradient(135deg,#10b981,#059669)">
            <div class="text-white-50 small">Fee Income {{ $year }}</div>
            <div class="fs-2 fw-bold">₹{{ number_format($feeIncome) }}</div>
            <i class="bi bi-arrow-up-circle-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-white" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)">
            <div class="text-white-50 small">Teacher Salary</div>
            <div class="fs-2 fw-bold">₹{{ number_format($teacherOut) }}</div>
            <i class="bi bi-people-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="text-white-50 small">Total Expenses</div>
            <div class="fs-2 fw-bold">₹{{ number_format($totalOut) }}</div>
            <i class="bi bi-arrow-down-circle-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card text-white" style="background:{{ $balance >= 0 ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#ef4444,#dc2626)' }}">
            <div class="text-white-50 small">Net Balance</div>
            <div class="fs-2 fw-bold">₹{{ number_format($balance) }}</div>
            <i class="bi bi-balance-scale stat-icon"></i>
        </div>
    </div>
</div>

<!-- Monthly Breakdown -->
<div class="card">
    <div class="card-header fw-semibold"><i class="bi bi-table me-2"></i>Monthly Breakdown – {{ $year }}</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Month</th>
                        <th class="text-success">Income (Fees)</th>
                        <th class="text-primary">Teacher Salary</th>
                        <th class="text-warning-emphasis">Staff Salary</th>
                        <th class="text-danger">Expenditure</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php $monthNames = ['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
                    @foreach($months as $m => $data)
                    @php $bal = $data['income'] - $data['teacher'] - $data['staff'] - $data['expense']; @endphp
                    <tr>
                        <td class="fw-medium">{{ $monthNames[$m] }} {{ $year }}</td>
                        <td class="text-success fw-semibold">₹{{ number_format($data['income']) }}</td>
                        <td>₹{{ number_format($data['teacher']) }}</td>
                        <td>₹{{ number_format($data['staff']) }}</td>
                        <td class="text-danger">₹{{ number_format($data['expense']) }}</td>
                        <td class="{{ $bal >= 0 ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
                            {{ $bal >= 0 ? '+' : '' }}₹{{ number_format($bal) }}
                        </td>
                    </tr>
                    @endforeach
                    <!-- Totals -->
                    <tr class="table-secondary fw-bold">
                        <td>TOTAL</td>
                        <td class="text-success">₹{{ number_format($feeIncome) }}</td>
                        <td>₹{{ number_format($teacherOut) }}</td>
                        <td>₹{{ number_format($staffOut) }}</td>
                        <td class="text-danger">₹{{ number_format($expOut) }}</td>
                        <td class="{{ $balance >= 0 ? 'text-success' : 'text-danger' }}">₹{{ number_format($balance) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
