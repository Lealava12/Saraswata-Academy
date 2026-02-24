<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceDetail;
use App\Models\StudentFee;
use App\Models\ExamMark;
use App\Models\StudentMaterial;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $student->load(['classInfo', 'board']);

        // Today's attendance
        $today = now()->toDateString();
        $todayAttendance = AttendanceDetail::where('student_id', $student->id)
            ->whereHas('attendance', fn($q) => $q->where('attendance_date', $today))
            ->first();

        // Next fee due
        $nextFee = StudentFee::where('student_id', $student->id)
            ->whereIn('status', ['Due', 'Overdue'])
            ->orderBy('due_date')
            ->first();

        // Last exam
        $lastExam = ExamMark::with(['exam.subject', 'exam.classInfo'])
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        // Latest material
        $latestMaterial = StudentMaterial::with('material')
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        // Overdue alert
        $overdueCount = StudentFee::where('student_id', $student->id)
            ->where('status', 'Overdue')->count();

        return view('student.dashboard.index', compact(
            'student',
            'todayAttendance',
            'nextFee',
            'lastExam',
            'latestMaterial',
            'overdueCount'
        ));
    }
}
