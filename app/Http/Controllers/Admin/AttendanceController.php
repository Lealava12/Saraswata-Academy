<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with(['classInfo', 'subject'])
            ->latest()->get();
        return view('admin.attendance.index', compact('attendances'));
    }

    public function create()
    {
        $classes = Classes::where('is_active', 1)->get();
        $subjects = Subject::where('is_active', 1)->get();
        return view('admin.attendance.create', compact('classes', 'subjects'));
    }

    public function getStudents(Request $request)
    {
        $students = Student::where('class_id', $request->class_id)
            ->where('is_active', 1)
            ->get(['id', 'name', 'student_id', 'roll_no']);
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subjects' => 'required|array|min:1',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array',
        ]);

        foreach ($request->subjects as $subjectId) {
            $attendance = Attendance::create([
                'class_id' => $request->class_id,
                'subject_id' => $subjectId,
                'attendance_date' => $request->attendance_date,
                'slug' => Str::slug('att-' . uniqid()),
            ]);

            foreach ($request->attendance as $studentId => $status) {
                AttendanceDetail::create([
                    'attendance_id' => $attendance->id,
                    'student_id' => $studentId,
                    'status' => $status,
                    'slug' => Str::slug('ad-' . uniqid()),
                ]);
            }
        }

        return redirect()->route('admin.attendance.index')->with('success', 'Attendance saved.');
    }

    public function show($id)
    {
        $attendance = Attendance::with(['classInfo', 'subject', 'details.student'])->findOrFail($id);
        return view('admin.attendance.show', compact('attendance'));
    }
}
