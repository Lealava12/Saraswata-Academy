<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceDetail;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\Exam;
use App\Models\StudentFee;
use App\Models\Expenditure;
use App\Models\TeacherSalary;
use App\Models\StaffSalary;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
     public function attendance(Request $request)
{
    $classes = Classes::where('is_active', 1)->get();
    $subjects = Subject::where('is_active', 1)->get();
    
    // Get students for dropdown - always get all students or based on class
    $students = Student::where('is_active', 1)
        ->when($request->class_id, function($q) use ($request) {
            return $q->where('class_id', $request->class_id);
        })
        ->orderBy('name')
        ->get(['id', 'name', 'student_id', 'roll_no']);
    
    // Build the query
    $query = Attendance::with(['classInfo', 'subjects', 'details.student'])
        ->when($request->class_id, function($q) use ($request) {
            return $q->where('class_id', $request->class_id);
        })
        ->when($request->subject_id, function($q) use ($request) {
            return $q->whereHas('subjects', function($subQuery) use ($request) {
                $subQuery->where('subject_id', $request->subject_id);
            });
        })
        ->when($request->from_date, function($q) use ($request) {
            return $q->whereDate('attendance_date', '>=', Carbon::parse($request->from_date)->format('Y-m-d'));
        })
        ->when($request->to_date, function($q) use ($request) {
            return $q->whereDate('attendance_date', '<=', Carbon::parse($request->to_date)->format('Y-m-d'));
        });

    // Get attendances
    $attendances = $query->orderBy('attendance_date', 'desc')->get();
    
    // If student is selected, filter the attendances to only show those where the student exists
    if ($request->filled('student_id')) {
        $attendances = $attendances->filter(function($attendance) use ($request) {
            return $attendance->details->contains('student_id', $request->student_id);
        });
    }
    
    // Calculate overall statistics
    $totalPresent = 0;
    $totalAbsent = 0;
    $totalRecords = 0;
    
    foreach ($attendances as $attendance) {
        // If student is selected, only count that student's attendance
        if ($request->filled('student_id')) {
            $studentDetail = $attendance->details->where('student_id', $request->student_id)->first();
            if ($studentDetail) {
                if ($studentDetail->status === 'Present') {
                    $totalPresent++;
                } else {
                    $totalAbsent++;
                }
                $totalRecords++;
            }
        } else {
            $present = $attendance->details->where('status', 'Present')->count();
            $absent = $attendance->details->where('status', 'Absent')->count();
            
            $totalPresent += $present;
            $totalAbsent += $absent;
            $totalRecords += $attendance->details->count();
        }
    }

    // Prepare chart data
    $chartLabels = [];
    $chartPresent = [];
    $chartAbsent = [];
    
    foreach ($attendances as $attendance) {
        $chartLabels[] = $attendance->attendance_date->format('d M Y');
        
        if ($request->filled('student_id')) {
            $studentDetail = $attendance->details->where('student_id', $request->student_id)->first();
            if ($studentDetail) {
                if ($studentDetail->status === 'Present') {
                    $chartPresent[] = 1;
                    $chartAbsent[] = 0;
                } else {
                    $chartPresent[] = 0;
                    $chartAbsent[] = 1;
                }
            } else {
                $chartPresent[] = 0;
                $chartAbsent[] = 0;
            }
        } else {
            $chartPresent[] = $attendance->details->where('status', 'Present')->count();
            $chartAbsent[] = $attendance->details->where('status', 'Absent')->count();
        }
    }

    return view('admin.reports.attendance', compact(
        'classes', 
        'subjects', 
        'students',
        'attendances', 
        'totalPresent', 
        'totalAbsent', 
        'totalRecords',
        'chartLabels',
        'chartPresent',
        'chartAbsent'
    ));
}

    public function getStudentsByClass(Request $request)
    {
        $students = Student::where('class_id', $request->class_id)
            ->where('is_active', 1)
            ->orderBy('roll_no')
            ->get(['id', 'name', 'student_id', 'roll_no']);
        
        return response()->json($students);
    }

     public function exportAttendancePdf(Request $request)
{
    // Build the query
    $query = Attendance::with(['classInfo', 'subjects', 'details.student'])
        ->when($request->class_id, function($q) use ($request) {
            return $q->where('class_id', $request->class_id);
        })
        ->when($request->subject_id, function($q) use ($request) {
            return $q->whereHas('subjects', function($subQuery) use ($request) {
                $subQuery->where('subject_id', $request->subject_id);
            });
        })
        ->when($request->student_id, function($q) use ($request) {
            return $q->whereHas('details', function($detailQuery) use ($request) {
                $detailQuery->where('student_id', $request->student_id);
            });
        })
        ->when($request->from_date, function($q) use ($request) {
            return $q->whereDate('attendance_date', '>=', Carbon::parse($request->from_date)->format('Y-m-d'));
        })
        ->when($request->to_date, function($q) use ($request) {
            return $q->whereDate('attendance_date', '<=', Carbon::parse($request->to_date)->format('Y-m-d'));
        })
        ->orderBy('attendance_date', 'desc')
        ->get();

    $data = [
        'attendances' => $query,
        'filters' => $request->all(),
        'generated_at' => now()->format('d-m-Y H:i:s')
    ];

    $pdf = Pdf::loadView('admin.reports.pdf.attendance', $data);
    return $pdf->download('attendance_report_' . date('Y-m-d') . '.pdf');
}

     


    public function exam(Request $request)
    {
        $classes = Classes::get();
        $subjects = Subject::get();
        $students = Student::get();

        $query = ExamMark::with(['exam.classInfo', 'exam.subject', 'student'])
            ->whereHas('exam', fn($q) => $q);

        if ($request->filled('class_id')) {
            $query->whereHas('exam', fn($q) => $q->where('class_id', $request->class_id));
        }
        if ($request->filled('subject_id')) {
            $query->whereHas('exam', fn($q) => $q->where('subject_id', $request->subject_id));
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereHas('exam', fn($q) => $q->whereBetween('exam_date', [$request->from_date, $request->to_date]));
        }

        $marks = $query->latest()->get();
        return view('admin.reports.exam', compact('marks', 'classes', 'subjects', 'students'));
    }

    public function fee(Request $request)
    {
        $classes = Classes::get();
        $students = Student::get();

        $query = StudentFee::with(['student', 'classInfo']);

        if ($request->filled('class_id'))
            $query->where('class_id', $request->class_id);
        if ($request->filled('student_id'))
            $query->where('student_id', $request->student_id);
        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('payment_mode'))
            $query->where('payment_mode', $request->payment_mode);
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('payment_date', [$request->from_date, $request->to_date]);
        }

        $fees = $query->latest()->get();
        $totalPaid = (clone $query)->where('status', 'Paid')->sum('amount');
        $totalDue = (clone $query)->where('status', 'Due')->sum('amount');
        $totalOverdue = (clone $query)->where('status', 'Overdue')->sum('amount');
        return view('admin.reports.fee', compact('fees', 'classes', 'students', 'totalPaid', 'totalDue', 'totalOverdue'));
    }

    public function financial(Request $request)
    {
        $year = $request->year ?? now()->year;

        $feeIncome = StudentFee::where('status', 'Paid')->whereYear('payment_date', $year)->sum('amount');
        $teacherOut = TeacherSalary::whereYear('payment_date', $year)->sum('amount');
        $staffOut = StaffSalary::whereYear('payment_date', $year)->sum('amount');
        $expOut = Expenditure::whereYear('expense_date', $year)->sum('amount');
        $totalOut = $teacherOut + $staffOut + $expOut;
        $balance = $feeIncome - $totalOut;

        // Monthly breakdown
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = [
                'income' => StudentFee::where('status', 'Paid')->whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount'),
                'teacher' => TeacherSalary::whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount'),
                'staff' => StaffSalary::whereYear('payment_date', $year)->whereMonth('payment_date', $m)->sum('amount'),
                'expense' => Expenditure::whereYear('expense_date', $year)->whereMonth('expense_date', $m)->sum('amount'),
            ];
        }

        return view('admin.reports.financial', compact('feeIncome', 'teacherOut', 'staffOut', 'expOut', 'totalOut', 'balance', 'months', 'year'));
    }

    public function exportPdf(Request $request)
    {
        $type = $request->type ?? 'fee';
        $data = [];

        if ($type === 'fee') {
            $data['records'] = StudentFee::with(['student', 'classInfo'])->latest()->get();
            $pdf = Pdf::loadView('admin.reports.pdf.fee', $data)->setPaper('a4', 'landscape');
        } elseif ($type === 'attendance') {
            $data['records'] = AttendanceDetail::with(['attendance.classInfo', 'attendance.subject', 'student'])->latest()->take(200)->get();
            $pdf = Pdf::loadView('admin.reports.pdf.attendance', $data)->setPaper('a4', 'landscape');
        } elseif ($type === 'exam') {
            $data['records'] = ExamMark::with(['exam.classInfo', 'exam.subject', 'student'])->latest()->take(200)->get();
            $pdf = Pdf::loadView('admin.reports.pdf.exam', $data)->setPaper('a4', 'landscape');
        } else {
            abort(404);
        }

        return $pdf->download("saraswata_{$type}_report.pdf");
    }

    public function exportCsv(Request $request)
    {
        $type = $request->type ?? 'fee';
        $filename = "saraswata_{$type}_report.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($type) {
            $out = fopen('php://output', 'w');
            if ($type === 'fee') {
                fputcsv($out, ['Receipt No', 'Student', 'Student ID', 'Class', 'Amount', 'Payment Date', 'Payment Mode', 'Due Date', 'Status']);
                StudentFee::with(['student', 'classInfo'])->chunk(200, function ($fees) use ($out) {
                    foreach ($fees as $fee) {
                        fputcsv($out, [
                            $fee->receipt_no,
                            $fee->student->name ?? '-',
                            $fee->student->student_id ?? '-',
                            $fee->classInfo->name ?? '-',
                            $fee->amount,
                            $fee->payment_date,
                            $fee->payment_mode,
                            $fee->due_date,
                            $fee->status,
                        ]);
                    }
                });
            } elseif ($type === 'attendance') {
                fputcsv($out, ['Date', 'Class', 'Subject', 'Student', 'Roll No', 'Status']);
                AttendanceDetail::with(['attendance.classInfo', 'attendance.subject', 'student'])->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $r) {
                        fputcsv($out, [
                            optional($r->attendance)->attendance_date,
                            optional(optional($r->attendance)->classInfo)->name ?? '-',
                            optional(optional($r->attendance)->subject)->name ?? '-',
                            optional($r->student)->name ?? '-',
                            optional($r->student)->roll_no ?? '-',
                            $r->status,
                        ]);
                    }
                });
            } elseif ($type === 'exam') {
                fputcsv($out, ['Date', 'Class', 'Subject', 'Full Marks', 'Student', 'Roll No', 'Marks Obtained']);
                ExamMark::with(['exam.classInfo', 'exam.subject', 'student'])->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $r) {
                        fputcsv($out, [
                            optional($r->exam)->exam_date,
                            optional(optional($r->exam)->classInfo)->name ?? '-',
                            optional(optional($r->exam)->subject)->name ?? '-',
                            optional($r->exam)->full_marks ?? '-',
                            optional($r->student)->name ?? '-',
                            optional($r->student)->roll_no ?? '-',
                            $r->marks_obtained,
                        ]);
                    }
                });
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
