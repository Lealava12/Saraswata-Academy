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
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
   public function index()
{
    $attendances = Attendance::with(['classInfo', 'subjects'])
        ->latest()
        ->get();
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
            ->orderBy('roll_no')
            ->get(['id', 'name', 'student_id', 'roll_no']);
        return response()->json($students);
    }

     public function store(Request $request)
{
    $request->validate([
        'class_id' => 'required|exists:classes,id',
        'subjects' => 'required|array|min:1',
        'subjects.*' => 'exists:subjects,id',
        'attendance_date' => 'required|date',
        'attendance' => 'required|array',
        'attendance.*' => 'in:Present,Absent'
    ]);

    try {
        DB::beginTransaction();

        // Create attendance record
        $attendance = Attendance::create([
            'class_id' => $request->class_id,
            'attendance_date' => $request->attendance_date,
            'slug' => Str::slug('att-' . uniqid()),
        ]);

        // Attach subjects using the relationship
        $attendance->subjects()->attach($request->subjects);

        // Create attendance details for each student
        foreach ($request->attendance as $studentId => $status) {
            AttendanceDetail::create([
                'attendance_id' => $attendance->id,
                'student_id' => $studentId,
                'status' => $status,
                'slug' => Str::slug('ad-' . uniqid()),
            ]);
        }

        DB::commit();
        return redirect()->route('admin.attendance.index')->with('success', 'Attendance saved successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error saving attendance: ' . $e->getMessage());
    }
}

    public function show($id)
{
    $attendance = Attendance::with(['classInfo', 'subjects', 'details.student'])
        ->findOrFail($id);
    
    return view('admin.attendance.show', compact('attendance'));
}
}