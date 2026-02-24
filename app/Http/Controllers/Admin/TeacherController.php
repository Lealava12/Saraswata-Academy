<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\TeacherSubject;
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
        return view('admin.teachers.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'mobile' => 'required|string|max:15',
            'joining_date' => 'required|date',
            'subjects' => 'array',
        ]);

        $teacher = Teacher::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'joining_date' => $request->joining_date,
            'is_active' => $request->is_active ?? 1,
            'slug' => Str::slug($request->name . '-' . uniqid()),
        ]);

        foreach ((array) $request->subjects as $subjectId) {
            TeacherSubject::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $subjectId,
                'slug' => Str::slug('ts-' . uniqid()),
            ]);
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher created.');
    }

    public function edit(Teacher $teacher)
    {
        $subjects = Subject::where('is_active', 1)->get();
        $assignedSubjects = $teacher->subjects->pluck('id')->toArray();
        return view('admin.teachers.edit', compact('teacher', 'subjects', 'assignedSubjects'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'mobile' => 'required|string|max:15',
            'joining_date' => 'required|date',
        ]);

        $data = $request->only('name', 'mobile', 'address', 'joining_date', 'is_active');
        $teacher->update($data);

        // Re-sync subjects
        TeacherSubject::where('teacher_id', $teacher->id)->delete();
        foreach ((array) $request->subjects as $subjectId) {
            TeacherSubject::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $subjectId,
                'slug' => Str::slug('ts-' . uniqid()),
            ]);
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Teacher removed.');
    }
}
