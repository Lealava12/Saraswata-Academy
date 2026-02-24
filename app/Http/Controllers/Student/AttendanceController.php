<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceDetail;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();

        $query = AttendanceDetail::where('student_id', $student->id)
            ->with(['attendance.subject', 'attendance.classInfo'])
            ->whereHas('attendance', fn($q) => $q);

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereHas(
                'attendance',
                fn($q) =>
                $q->whereBetween('attendance_date', [$request->from_date, $request->to_date])
            );
        }

        $records = $query->latest()->get();

        $presentCount = $records->where('status', 'Present')->count();
        $absentCount = $records->where('status', 'Absent')->count();
        $total = $records->count();
        $percentage = $total > 0 ? round(($presentCount / $total) * 100, 1) : 0;

        // Subject-wise summary
        $subjectSummary = $records->groupBy(fn($r) => optional(optional($r->attendance)->subject)->name ?? 'Unknown')
            ->map(fn($group) => [
                'present' => $group->where('status', 'Present')->count(),
                'absent' => $group->where('status', 'Absent')->count(),
                'total' => $group->count(),
            ]);

        return view('student.attendance.index', compact(
            'records',
            'presentCount',
            'absentCount',
            'total',
            'percentage',
            'subjectSummary',
            'student'
        ));
    }
}
