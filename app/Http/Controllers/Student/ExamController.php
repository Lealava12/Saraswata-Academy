<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamMark;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();

        $query = ExamMark::where('student_id', $student->id)
            ->with(['exam.subject', 'exam.classInfo'])
            ->whereHas('exam', fn($q) => $q);

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereHas(
                'exam',
                fn($q) =>
                $q->whereBetween('exam_date', [$request->from_date, $request->to_date])
            );
        }

        $marks = $query->latest()->get();

        $avgPercentage = $marks->count() > 0
            ? round($marks->sum(fn($m) => $m->exam->full_marks > 0 ? ($m->marks_obtained / $m->exam->full_marks) * 100 : 0) / $marks->count(), 1)
            : 0;

        return view('student.exams.index', compact('marks', 'avgPercentage', 'student'));
    }
}
