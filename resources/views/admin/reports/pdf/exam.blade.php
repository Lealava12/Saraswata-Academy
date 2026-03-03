<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Exam Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #ddd; padding:6px; }
        th { background:#f3f3f3; }
    </style>
</head>
<body>
    <h3>Exam Performance Report</h3>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Student</th>
                <th>Marks</th>
                <th>%</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marks as $m)
                @php
                    $full = $m->exam->full_marks ?? 0;
                    $pct = $full > 0 ? round(($m->marks_obtained/$full)*100, 1) : 0;
                    $grade = $pct>=90?'A+':($pct>=75?'A':($pct>=60?'B':($pct>=45?'C':'F')));
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($m->exam->exam_date)->format('d M Y') }}</td>
                    <td>{{ ($m->exam->classInfo->name ?? '-') . ($m->exam->classInfo->board->name ? ' (' . $m->exam->classInfo->board->name . ')' : '') }}</td>
                    <td>{{ $m->exam->subject->name ?? '-' }}</td>
                    <td>{{ $m->student->name ?? '-' }}</td>
                    <td>{{ $m->marks_obtained }} / {{ $full }}</td>
                    <td>{{ $pct }}%</td>
                    <td>{{ $grade }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>