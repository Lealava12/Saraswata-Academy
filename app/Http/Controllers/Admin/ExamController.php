<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with(['classInfo', 'subject'])->latest()->get();
        return view('admin.exams.index', compact('exams'));
    }

    public function create()
    {
        $classes = Classes::where('is_active', 1)->get();
        $subjects = Subject::where('is_active', 1)->get();
        return view('admin.exams.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'full_marks' => 'required|integer|min:1',
        ]);

        Exam::create([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'exam_date' => $request->exam_date,
            'full_marks' => $request->full_marks,
            'is_active' => $request->is_active ?? 1,
            'slug' => Str::slug('exam-' . uniqid()),
        ]);

        return redirect()->route('admin.exams.index')->with('success', 'Exam created.');
    }

    public function edit(Exam $exam)
    {
        $classes = Classes::where('is_active', 1)->get();
        $subjects = Subject::where('is_active', 1)->get();
        return view('admin.exams.edit', compact('exam', 'classes', 'subjects'));
    }

    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date' => 'required|date',
            'full_marks' => 'required|integer|min:1',
        ]);
        $exam->update($request->only('class_id', 'subject_id', 'exam_date', 'full_marks', 'is_active'));
        return redirect()->route('admin.exams.index')->with('success', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam removed.');
    }

    public function marks(Exam $exam)
    {
        $exam->load(['classInfo', 'subject']);
        $students = Student::where('class_id', $exam->class_id)->where('is_active', 1)->get();
        $existingMarks = ExamMark::where('exam_id', $exam->id)->get()->keyBy('student_id');
        return view('admin.exams.marks', compact('exam', 'students', 'existingMarks'));
    }

    public function storeMarks(Request $request, Exam $exam)
    {
        $request->validate(['marks' => 'required|array']);

        foreach ($request->marks as $studentId => $marksObtained) {
            ExamMark::updateOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $studentId],
                ['marks_obtained' => $marksObtained, 'slug' => Str::slug('em-' . uniqid())]
            );
        }

        return redirect()->route('admin.exams.index')->with('success', 'Marks saved.');
    }
}
