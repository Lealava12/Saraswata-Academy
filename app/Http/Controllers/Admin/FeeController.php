<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentFee;
use App\Models\Student;
use App\Models\Classes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeeController extends Controller
{
    public function index()
    {
        $fees = StudentFee::with(['student', 'classInfo'])->latest()->get();
        return view('admin.fees.index', compact('fees'));
    }

    public function create()
    {
        $students = Student::where('is_active', 1)->with('classInfo')->get();
        $classes = Classes::where('is_active', 1)->get();
        return view('admin.fees.create', compact('students', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|in:UPI,Cash,Bank',
            'due_date' => 'required|date',
            'mpin' => 'required',
        ]);

        // Verify admin MPIN (stored in config)
        if ($request->mpin !== env('ADMIN_SALARY_MPIN')) {
            return back()->withErrors(['mpin' => 'Invalid MPIN'])->withInput();
        }

        $student = Student::with('classInfo')->findOrFail($request->student_id);
        $classAmt = $student->classInfo ? $student->classInfo->monthly_fee : null;

        if ($classAmt && abs($request->amount - $classAmt) > 0.01) {
            session(['fee_amount_mismatch' => true, 'fee_class_amount' => $classAmt]);
        }

        $status = StudentFee::computeStatus($request->due_date, $request->payment_date);

        StudentFee::create([
            'student_id' => $request->student_id,
            'class_id' => $student->class_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_mode' => $request->payment_mode,
            'due_date' => $request->due_date,
            'status' => $status,
            'slug' => Str::slug('fee-' . uniqid()),
            'receipt_no' => 'RCT-' . strtoupper(Str::random(8)),
        ]);

        return redirect()->route('admin.fees.index')->with('success', 'Fee entry recorded.');
    }

    public function verifyMpin(Request $request)
    {
        if ($request->mpin === env('ADMIN_SALARY_MPIN')) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid MPIN']);
    }
}
