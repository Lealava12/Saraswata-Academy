<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Classes;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeeController extends Controller
{
    public function index()
    {
        $fees = StudentFee::with(['student', 'classInfo'])->latest()->paginate(20);
        return view('admin.fees.index', compact('fees'));
    }

    public function pending(Request $request)
    {
        $boards = Board::all();
        $classes = Classes::all();

        $query = Student::where('is_active', 1)
            ->with(['classInfo', 'board', 'fees'])
            ->when($request->board_id, fn($q) => $q->where('board_id', $request->board_id))
            ->when($request->class_id, fn($q) => $q->where('class_id', $request->class_id));

        $allStudents = $query->get();

        $pendingStudents = $allStudents->filter(function ($student) {
            return $student->getBalanceDue() > 0;
        });

        return view('admin.fees.pending', compact('pendingStudents', 'boards', 'classes'));
    }

    public function create(Request $request)
{
    $students = Student::where('is_active', 1)
        ->select('id','name','student_id','class_id','board_id','monthly_fees','joining_date')
        ->get();

    $paidMap = StudentFee::selectRaw('student_id, COALESCE(SUM(amount),0) as total_paid')
        ->groupBy('student_id')
        ->pluck('total_paid', 'student_id');

    $selectedStudentId = (int) $request->query('student_id');
    $selectedBoardId = null;
    $selectedClassId = null;

    if ($selectedStudentId) {
        $s = $students->firstWhere('id', $selectedStudentId);
        if ($s) {
            $selectedBoardId = (int) $s->board_id;
            $selectedClassId = (int) $s->class_id;
        }
    }

    // ✅ include selected board even if inactive
    $boardsQuery = Board::query();
    $boardsQuery->where('is_active', 1);

    if ($selectedBoardId) {
        $boardsQuery->orWhere('id', $selectedBoardId);
    }

    $boards  = $boardsQuery->orderBy('name')->get();

    // ✅ include selected class even if inactive
    $classesQuery = Classes::query();
    $classesQuery->where('is_active', 1);

    if ($selectedClassId) {
        $classesQuery->orWhere('id', $selectedClassId);
    }

    $classes = $classesQuery->orderBy('name')->get();

    return view('admin.fees.create', compact(
        'boards','classes','students','paidMap',
        'selectedStudentId','selectedBoardId','selectedClassId'
    ));
}


    public function store(Request $request)
    {
        $request->validate([
            'board_id' => 'required|exists:boards,id',
            'class_id' => 'required|exists:classes,id',
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|in:UPI,Cash,Bank',
            'due_date' => 'required|date',
        ]);

        $student = Student::findOrFail($request->student_id);

        StudentFee::create([
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_mode' => $request->payment_mode,
            'due_date' => $request->due_date,
            'status' => 'Paid',
            'slug' => Str::slug('fee-' . uniqid()),
            'receipt_no' => 'RCT-' . strtoupper(Str::random(8)),
        ]);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee entry recorded successfully.');
    }

    public function verifyMpin(Request $request)
    {
        if ($request->mpin === config('app.admin_salary_mpin')) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid MPIN']);
    }
}