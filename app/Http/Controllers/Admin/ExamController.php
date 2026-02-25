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
use Barryvdh\DomPDF\Facade\Pdf;

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
        // Check if marks were entered (not empty)
        if ($marksObtained !== null && $marksObtained !== '') {
            ExamMark::updateOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $studentId],
                [
                    'marks_obtained' => $marksObtained, 
                    'slug' => Str::slug('em-' . uniqid())
                ]
            );
        } else {
             ExamMark::where('exam_id', $exam->id)
                    ->where('student_id', $studentId)
                    ->delete();
            
           
        }
    }

    return redirect()->route('admin.exams.index')->with('success', 'Marks saved.');
}
   // In ExamController.php - Add this method if missing

public function viewMarks(Exam $exam)
{
    $exam->load(['classInfo', 'subject']);
    $marks = ExamMark::with('student')
        ->where('exam_id', $exam->id)
        ->get();
    
    return view('admin.exams.view-marks', compact('exam', 'marks'));
}
    public function exportPdf()
{
    $exams = Exam::with(['classInfo', 'subject'])->latest()->get();
    
    $pdf = Pdf::loadView('admin.exams.exports.exams-pdf', compact('exams'));
    
    $filename = 'exams_list_' . now()->format('Y-m-d') . '.pdf';
    
    return $pdf->download($filename);
}

/**
 * Export marks for a specific exam as PDF
 */
public function exportMarksPdf(Exam $exam)
{
    $exam->load(['classInfo', 'subject']);
    $marks = ExamMark::with('student')
        ->where('exam_id', $exam->id)
        ->get();
    
    $pdf = Pdf::loadView('admin.exams.exports.marks-pdf', compact('exam', 'marks'));
    
    $filename = 'marks_' . $exam->classInfo->name . '_' . $exam->subject->name . '_' . 
                now()->format('Y-m-d') . '.pdf';
    
    return $pdf->download($filename);
}
public function exportCsv()
{
    $exams = Exam::with(['classInfo', 'subject'])->latest()->get();
    
    $filename = 'exams_list_' . now()->format('Y-m-d') . '.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];
    
    $callback = function() use ($exams) {
        $file = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($file, ['#', 'Class', 'Subject', 'Exam Date', 'Full Marks', 'Status']);
        
        // Add data
        foreach ($exams as $i => $exam) {
            fputcsv($file, [
                $i + 1,
                $exam->classInfo->name ?? '-',
                $exam->subject->name ?? '-',
                $exam->exam_date,
                $exam->full_marks,
                $exam->is_active ? 'Active' : 'Inactive'
            ]);
        }
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}

/**
 * Export marks for a specific exam as CSV
 */
public function exportMarksCsv(Exam $exam)
{
    $exam->load(['classInfo', 'subject']);
    $marks = ExamMark::with('student')
        ->where('exam_id', $exam->id)
        ->get();
    
    $filename = 'marks_' . $exam->classInfo->name . '_' . $exam->subject->name . '_' . 
                now()->format('Y-m-d') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ];
    
    $callback = function() use ($exam, $marks) {
        $file = fopen('php://output', 'w');
        
        // Add metadata
        fputcsv($file, ['Exam:', $exam->classInfo->name . ' - ' . $exam->subject->name]);
        fputcsv($file, ['Date:', $exam->exam_date]);
        fputcsv($file, ['Full Marks:', $exam->full_marks]);
        fputcsv($file, []); // Empty row
        
        // Add headers
        fputcsv($file, ['#', 'Student ID', 'Roll No', 'Student Name', 'Marks Obtained', 'Percentage', 'Status']);
        
        // Add data
        foreach ($marks as $i => $mark) {
            $percentage = $mark->marks_obtained ? 
                number_format(($mark->marks_obtained / $exam->full_marks) * 100, 1) . '%' : 'N/A';
            
            $status = $this->getStatusForExport($mark, $exam);
            
            fputcsv($file, [
                $i + 1,
                $mark->student->student_id ?? 'N/A',
                $mark->student->roll_no ?? 'N/A',
                $mark->student->name ?? 'N/A',
                $mark->marks_obtained ?? 'Not entered',
                $percentage,
                $status
            ]);
        }
        
        // Add summary
        if ($marks->whereNotNull('marks_obtained')->count() > 0) {
            fputcsv($file, []); // Empty row
            fputcsv($file, ['Summary']);
            fputcsv($file, ['Total Students:', $marks->count()]);
            fputcsv($file, ['Marks Entered:', $marks->whereNotNull('marks_obtained')->count()]);
            fputcsv($file, ['Average:', number_format($marks->avg('marks_obtained'), 1)]);
            fputcsv($file, ['Highest:', $marks->max('marks_obtained')]);
            fputcsv($file, ['Lowest:', $marks->min('marks_obtained')]);
        }
        
        fclose($file);
    };
    
    return response()->stream($callback, 200, $headers);
}

/**
 * Helper method for status in exports
 */
private function getStatusForExport($mark, $exam)
{
    if (!$mark->marks_obtained) return 'Pending';
    
    $percentage = ($mark->marks_obtained / $exam->full_marks) * 100;
    
    if ($percentage >= 75) return 'Distinction';
    if ($percentage >= 60) return 'First Class';
    if ($percentage >= 45) return 'Second Class';
    if ($percentage >= 33) return 'Pass';
    return 'Fail';
}
}