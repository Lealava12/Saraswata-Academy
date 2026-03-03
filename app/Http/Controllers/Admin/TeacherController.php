<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with('subjects')->latest()->get();
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        if (!session('mpin_unlocked')) {
            return redirect()->route('admin.teachers.index')->with('error', 'Authentication required.');
        }
        session()->forget('mpin_unlocked');
        $subjects = Subject::where('is_active', 1)->get();
        $classes = Classes::where('is_active', 1)
            ->with('board:id,name') // load board
            ->orderBy('board_id')
            ->orderBy('name')
            ->get();
        return view('admin.teachers.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'mobile' => 'required|string|max:15',
            'joining_date' => 'required|date',
            'subjects' => 'array',
            'classes' => 'array',
            'class_salaries' => 'array',
        ]);

        $teacher = Teacher::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'joining_date' => $request->joining_date,
            'is_active' => $request->is_active ?? 1,
            'slug' => Str::slug($request->name . '-' . uniqid()),
        ]);


        foreach ((array)$request->subjects as $subjectId) {
            TeacherSubject::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $subjectId,
                'slug' => Str::slug('ts-' . uniqid()),
            ]);
        }

        // Attach Classes with their corresponding salaries
        if ($request->has('classes') && is_array($request->classes)) {
            $classData = [];
            foreach ($request->classes as $classId) {
                $amount = $request->input("class_salaries.{$classId}");
                $classData[$classId] = ['amount' => $amount ?: 0];
            }
            $teacher->classes()->attach($classData);
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created.');
    }

    public function edit(Teacher $teacher)
    {
        $subjects = Subject::where('is_active', 1)->get();
        $assignedSubjects = $teacher->subjects->pluck('id')->toArray();
        $classes = Classes::where('is_active', 1)
            ->with('board:id,name') // load board
            ->orderBy('board_id')
            ->orderBy('name')
            ->get();
        $classSalaries = $teacher->classes->pluck('pivot.amount', 'id')->toArray();
        return view('admin.teachers.edit', compact('teacher', 'subjects', 'assignedSubjects', 'classes', 'classSalaries'));
    }    public function show(Teacher $teacher)    {
        $teacher->load(['subjects', 'classes.board']);
        return view('admin.teachers.show', compact('teacher'));    }
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'mobile' => 'required|string|max:15',
            'joining_date' => 'required|date',
            'subjects' => 'array',
            'classes' => 'array',
            'class_salaries' => 'array',
        ]);

        $data = $request->only('name', 'mobile', 'address', 'joining_date', 'is_active');
        $teacher->update($data);

        // Re-sync subjects
        TeacherSubject::where('teacher_id', $teacher->id)->delete();
        foreach ((array)$request->subjects as $subjectId) {
            TeacherSubject::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $subjectId,
                'slug' => Str::slug('ts-' . uniqid()),
            ]);
        }

        // Re-sync classes and salaries
        $classData = [];
        if ($request->has('classes') && is_array($request->classes)) {
            foreach ($request->classes as $classId) {
                $amount = $request->input("class_salaries.{$classId}");
                $classData[$classId] = ['amount' => $amount ?: 0];
            }
        }
        $teacher->classes()->sync($classData);

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher removed.');
    }

    public function verifyMpin(Request $request)
    {
        if ($request->mpin === config('app.admin_salary_mpin')) {
            session(['mpin_unlocked' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid MPIN']);
    }
}
