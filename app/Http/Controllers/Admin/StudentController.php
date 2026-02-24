<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentFee;
use App\Models\Classes;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with(['classInfo', 'board'])
            ->latest()->get();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $classes = Classes::where('is_active', 1)->with('board')->get();
        $boards = Board::where('is_active', 1)->get();
        $nextId = Student::generateStudentId();
        return view('admin.students.create', compact('classes', 'boards', 'nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:students,email',
            'mobile' => 'required|string|max:15',
            'password' => 'required|min:6',
            'dob' => 'nullable|date',
            'school_name' => 'nullable|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'board_id' => 'required|exists:boards,id',
            'joining_date' => 'required|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'email', 'mobile', 'dob', 'school_name', 'class_id', 'board_id', 'joining_date', 'is_active');
        $data['password'] = Hash::make($request->password);
        $data['student_id'] = Student::generateStudentId();
        $data['slug'] = Str::slug($request->name . '-' . uniqid());

        // Auto roll_no per class
        $maxRoll = Student::where('class_id', $request->class_id)->max('roll_no');
        $data['roll_no'] = ($maxRoll ?? 0) + 1;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('students/photos', 'public');
            $data['photo'] = $path;
        }

        $student = Student::create($data);

        // Parent info
        if ($request->filled('father_name') || $request->filled('mother_name')) {
            StudentParent::create([
                'student_id' => $student->id,
                'father_name' => $request->father_name,
                'father_mobile' => $request->father_mobile,
                'mother_name' => $request->mother_name,
                'mother_mobile' => $request->mother_mobile,
                'address' => $request->address,
                'slug' => Str::slug('sp-' . uniqid()),
            ]);
        }

        return redirect()->route('admin.students.index')
            ->with('success', "Student created. ID: {$data['student_id']} | Roll No: {$data['roll_no']}");
    }

    public function show(Student $student)
    {
        $student->load(['classInfo', 'board', 'parent', 'fees', 'attendances.attendance', 'examMarks.exam', 'materials.material']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = Classes::where('is_active', 1)->with('board')->get();
        $boards = Board::where('is_active', 1)->get();
        $student->load('parent');
        return view('admin.students.edit', compact('student', 'classes', 'boards'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'mobile' => 'required|string|max:15',
            'class_id' => 'required|exists:classes,id',
            'board_id' => 'required|exists:boards,id',
            'joining_date' => 'required|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'email', 'mobile', 'dob', 'school_name', 'class_id', 'board_id', 'joining_date', 'is_active');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('students/photos', 'public');
            $data['photo'] = $path;
        }
        $student->update($data);

        // Update parent
        $parentData = [
            'father_name' => $request->father_name,
            'father_mobile' => $request->father_mobile,
            'mother_name' => $request->mother_name,
            'mother_mobile' => $request->mother_mobile,
            'address' => $request->address,
        ];
        if ($student->parent) {
            $student->parent->update($parentData);
        } else {
            StudentParent::create(array_merge($parentData, [
                'student_id' => $student->id,
                'slug' => Str::slug('sp-' . uniqid()),
            ]));
        }

        return redirect()->route('admin.students.index')->with('success', 'Student updated.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Student removed.');
    }

    public function feeHistory(Student $student)
    {
        $fees = StudentFee::where('student_id', $student->id)->latest()->get();
        return view('admin.students.fee-history', compact('student', 'fees'));
    }
}
