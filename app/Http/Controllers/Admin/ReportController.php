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
        
        // Get students for dropdown - based on class if selected
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
        $filteredAttendances = collect();
        if ($request->filled('student_id')) {
            foreach ($attendances as $attendance) {
                $studentDetail = $attendance->details->where('student_id', $request->student_id)->first();
                if ($studentDetail) {
                    // Create a new collection item with attendance and the specific student detail
                    $filteredAttendances->push((object)[
                        'id' => $attendance->id,
                        'attendance_date' => $attendance->attendance_date,
                        'classInfo' => $attendance->classInfo,
                        'subjects' => $attendance->subjects,
                        'details' => collect([$studentDetail]), // Only include the selected student
                        'student_detail' => $studentDetail // Add this for easy access
                    ]);
                }
            }
            $attendances = $filteredAttendances;
        }
        
        // Calculate overall statistics
        $totalPresent = 0;
        $totalAbsent = 0;
        $totalRecords = 0;
        
        foreach ($attendances as $attendance) {
            if ($request->filled('student_id')) {
                // For student-specific view, each attendance record has only one detail
                if (isset($attendance->student_detail)) {
                    if ($attendance->student_detail->status === 'Present') {
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
            
            if ($request->filled('student_id') && isset($attendance->student_detail)) {
                if ($attendance->student_detail->status === 'Present') {
                    $chartPresent[] = 1;
                    $chartAbsent[] = 0;
                } else {
                    $chartPresent[] = 0;
                    $chartAbsent[] = 1;
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

    public function exportAttendanceCsv(Request $request)
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
            ->when($request->from_date, function($q) use ($request) {
                return $q->whereDate('attendance_date', '>=', Carbon::parse($request->from_date)->format('Y-m-d'));
            })
            ->when($request->to_date, function($q) use ($request) {
                return $q->whereDate('attendance_date', '<=', Carbon::parse($request->to_date)->format('Y-m-d'));
            })
            ->orderBy('attendance_date', 'desc');

        $attendances = $query->get();

        // Set headers for CSV download
        $filename = 'attendance_report_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($attendances, $request) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel to handle special characters and dates properly
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($handle, [
                'Date', 
                'Class', 
                'Subjects', 
                'Student ID', 
                'Student Name', 
                'Roll No', 
                'Status'
            ]);
            
            // Add data
            foreach ($attendances as $attendance) {
                $subjects = $attendance->subjects->pluck('name')->implode(', ');
                $date = $attendance->attendance_date ? $attendance->attendance_date->format('d-m-Y') : '';
                
                // If specific student is selected, only export that student's data
                if ($request->filled('student_id')) {
                    $detail = $attendance->details->where('student_id', $request->student_id)->first();
                    if ($detail) {
                        fputcsv($handle, [
                            $date,
                            $attendance->classInfo->name ?? '-',
                            $subjects,
                            $detail->student->student_id ?? '-',
                            $detail->student->name ?? '-',
                            $detail->student->roll_no ?? '-',
                            $detail->status
                        ]);
                    }
                } else {
                    // Export all students
                    foreach ($attendance->details as $detail) {
                        fputcsv($handle, [
                            $date,
                            $attendance->classInfo->name ?? '-',
                            $subjects,
                            $detail->student->student_id ?? '-',
                            $detail->student->name ?? '-',
                            $detail->student->roll_no ?? '-',
                            $detail->status
                        ]);
                    }
                }
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportAttendancePdf(Request $request)
    {
        // Build the query
        $attendances = Attendance::with(['classInfo', 'subjects', 'details.student'])
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
            })
            ->orderBy('attendance_date', 'desc')
            ->get();

        // If student is selected, filter the data
        if ($request->filled('student_id')) {
            $filteredAttendances = collect();
            foreach ($attendances as $attendance) {
                $detail = $attendance->details->where('student_id', $request->student_id)->first();
                if ($detail) {
                    $attendance->student_detail = $detail;
                    $filteredAttendances->push($attendance);
                }
            }
            $attendances = $filteredAttendances;
        }

        $data = [
            'attendances' => $attendances,
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
            $data['records'] = AttendanceDetail::with(['attendance.classInfo', 'attendance.subjects', 'student'])->latest()->take(200)->get();
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
        $filename = "saraswata_{$type}_report_" . date('Y-m-d_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($type) {
            $out = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            
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
                            $fee->payment_date ? date('d-m-Y', strtotime($fee->payment_date)) : '-',
                            $fee->payment_mode,
                            $fee->due_date ? date('d-m-Y', strtotime($fee->due_date)) : '-',
                            $fee->status,
                        ]);
                    }
                });
            } elseif ($type === 'attendance') {
                fputcsv($out, ['Date', 'Class', 'Subject', 'Student', 'Roll No', 'Status']);
                AttendanceDetail::with(['attendance.classInfo', 'attendance.subjects', 'student'])->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $r) {
                        $subjects = $r->attendance->subjects->pluck('name')->implode(', ');
                        fputcsv($out, [
                            $r->attendance->attendance_date ? date('d-m-Y', strtotime($r->attendance->attendance_date)) : '-',
                            optional(optional($r->attendance)->classInfo)->name ?? '-',
                            $subjects,
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
                            optional($r->exam)->exam_date ? date('d-m-Y', strtotime($r->exam->exam_date)) : '-',
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