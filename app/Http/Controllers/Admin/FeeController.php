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
        $fees = StudentFee::with(['student', 'classInfo'])->latest()->get();
        return view('admin.fees.index', compact('fees'));
    }

    public function create(Request $request)
{
    $boards  = Board::where('is_active', 1)->get();
    $classes = Classes::where('is_active', 1)->get();

    $students = Student::where('is_active', 1)
        ->select('id','name','student_id','class_id','board_id','monthly_fees','joining_date')
        ->get();

    $paidMap = StudentFee::selectRaw('student_id, COALESCE(SUM(amount),0) as total_paid')
        ->groupBy('student_id')
        ->pluck('total_paid', 'student_id');

    // ✅ preselect values if coming from fee-history add button
    $selectedStudentId = $request->query('student_id');
    $selectedBoardId = null;
    $selectedClassId = null;

    if ($selectedStudentId) {
        $s = $students->firstWhere('id', (int)$selectedStudentId);
        if ($s) {
            $selectedBoardId = $s->board_id;
            $selectedClassId = $s->class_id;
        }
    }

    return view('admin.fees.create', compact(
        'boards','classes','students','paidMap',
        'selectedStudentId','selectedBoardId','selectedClassId'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'board_id'      => 'required|exists:boards,id',
            'class_id'      => 'required|exists:classes,id',
            'student_id'    => 'required|exists:students,id',
            'amount'        => 'required|numeric|min:0',
            'payment_date'  => 'required|date',
            'payment_mode'  => 'required|in:UPI,Cash,Bank',
            'due_date'      => 'required|date',
        ]);

        $student = Student::findOrFail($request->student_id);

        StudentFee::create([
            'student_id'   => $student->id,
            'class_id'     => $student->class_id,
            'amount'       => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_mode' => $request->payment_mode,
            'due_date'     => $request->due_date,
            'status'       => 'Paid',
            'slug'         => Str::slug('fee-' . uniqid()),
            'receipt_no'   => 'RCT-' . strtoupper(Str::random(8)),
        ]);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee entry recorded successfully.');
    }
}