{{-- resources/views/admin/exams/exports/marks-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Exam Marks - {{ $exam->classInfo->name }} - {{ $exam->subject->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h2 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-box {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 5px;
        }
        table.marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.marks-table th {
            background: #4a5568;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        table.marks-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        table.marks-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-center {
            text-align: center;
        }
        .status-distinction { color: #059669; font-weight: bold; }
        .status-first { color: #2563eb; font-weight: bold; }
        .status-second { color: #d97706; font-weight: bold; }
        .status-pass { color: #6b7280; }
        .status-fail { color: #dc2626; font-weight: bold; }
        .status-pending { color: #9ca3af; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .summary table {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Exam Marks Report</h2>
        <p>{{ $exam->classInfo->name ?? 'N/A' }} - {{ $exam->subject->name ?? 'N/A' }}</p>
        <p>Exam Date: {{ \Carbon\Carbon::parse($exam->exam_date)->format('d F Y') }}</p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td width="25%"><strong>Full Marks:</strong> {{ $exam->full_marks }}</td>
                <td width="25%"><strong>Total Students:</strong> {{ $marks->count() }}</td>
                <td width="25%"><strong>Average:</strong> 
                    @php
                        $avg = $marks->whereNotNull('marks_obtained')->avg('marks_obtained');
                    @endphp
                    {{ $avg ? number_format($avg, 1) : 'N/A' }}
                </td>
                <td width="25%"><strong>Generated:</strong> {{ now()->format('d-m-Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table class="marks-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Roll No</th>
                <th>Student Name</th>
                <th>Marks Obtained</th>
                <th>Percentage</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($marks as $i => $mark)
            <tr>
                <td class="text-center">{{ $i+1 }}</td>
                <td>{{ $mark->student->student_id ?? 'N/A' }}</td>
                <td class="text-center">{{ $mark->student->roll_no ?? 'N/A' }}</td>
                <td>{{ $mark->student->name ?? 'N/A' }}</td>
                <td class="text-center">
                    @if($mark->marks_obtained !== null)
                        {{ $mark->marks_obtained }}
                    @else
                        <span class="status-pending">Not entered</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($mark->marks_obtained !== null)
                        {{ number_format(($mark->marks_obtained / $exam->full_marks) * 100, 1) }}%
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($mark->marks_obtained !== null)
                        @php
                            $percentage = ($mark->marks_obtained / $exam->full_marks) * 100;
                        @endphp
                        @if($percentage >= 75)
                            <span class="status-distinction">Distinction</span>
                        @elseif($percentage >= 60)
                            <span class="status-first">First Class</span>
                        @elseif($percentage >= 45)
                            <span class="status-second">Second Class</span>
                        @elseif($percentage >= 33)
                            <span class="status-pass">Pass</span>
                        @else
                            <span class="status-fail">Fail</span>
                        @endif
                    @else
                        <span class="status-pending">Pending</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No marks found for this exam</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($marks->whereNotNull('marks_obtained')->count() > 0)
    <div class="summary">
        <h4>Summary Statistics</h4>
        <table>
            <tr>
                <td><strong>Total Students:</strong> {{ $marks->count() }}</td>
                <td><strong>Marks Entered:</strong> {{ $marks->whereNotNull('marks_obtained')->count() }}</td>
                <td><strong>Passed:</strong> {{ $marks->where('marks_obtained', '>=', $exam->full_marks * 0.33)->count() }}</td>
            </tr>
            <tr>
                <td><strong>Average Marks:</strong> {{ number_format($marks->avg('marks_obtained'), 1) }}</td>
                <td><strong>Highest Marks:</strong> {{ $marks->max('marks_obtained') }}</td>
                <td><strong>Lowest Marks:</strong> {{ $marks->min('marks_obtained') }}</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="footer">
        Generated on {{ now()->format('d F Y h:i A') }}
    </div>
</body>
</html>