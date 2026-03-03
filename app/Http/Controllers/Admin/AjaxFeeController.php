<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class AjaxFeeController extends Controller
{
    /**
     * Get students by class for fee management.
     */
    public function getStudentsByClass(Request $request)
    {
        $students = Student::where('class_id', $request->class_id)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'student_id', 'roll_no']);

        return response()->json($students);
    }
}
