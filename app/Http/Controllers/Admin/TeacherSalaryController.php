<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherSalary;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeacherSalaryController extends Controller
{
    public function index()
    {
        $salaries = TeacherSalary::with('teacher')->latest()->get();
        return view('admin.teacher-salary.index', compact('salaries'));
    }

    public function create()
    {
        if (!session('mpin_unlocked')) {
            return redirect()->route('admin.teacher-salary.index')->with('error', 'Authentication required.');
        }
        session()->forget('mpin_unlocked'); // Force re-auth for next enter
        // $teachers = Teacher::where('is_active', 1)->with('classes')->get();
        $teachers = Teacher::where('is_active', 1)
            ->with(['classes.board:id,name']) // load board with class
            ->get();
        return view('admin.teacher-salary.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'amount' => 'required|numeric|min:0',
            'payment_month' => 'required|string',
            'payment_date' => 'required|date',
            'class_count' => 'nullable|integer|min:0',
        ]);


        TeacherSalary::create([
            'teacher_id' => $request->teacher_id,
            'amount' => $request->amount,
            'payment_month' => $request->payment_month,
            'payment_date' => $request->payment_date,
            'class_count' => $request->class_count ?? 0,
            'breakdown' => $request->breakdown,
            'slug' => Str::slug('tsal-' . uniqid()),
        ]);

        return redirect()->route('admin.teacher-salary.index')->with('success', 'Salary recorded.');
    }

    public function destroy(TeacherSalary $teacherSalary)
    {
        $teacherSalary->delete();
        return redirect()->route('admin.teacher-salary.index')->with('success', 'Record removed.');
    }

    public function verifyMpin(Request $request)
    {
        if ($request->mpin === config('app.admin_salary_mpin')) {
            session(['mpin_unlocked' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid MPIN']);
    }
    public function show(TeacherSalary $teacherSalary)
    {
        $teacherSalary->load('teacher');
        return view('admin.teacher-salary.show', compact('teacherSalary'));
    }
}