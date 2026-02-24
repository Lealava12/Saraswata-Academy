<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentMaterial;

class MaterialController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $materials = StudentMaterial::where('student_id', $student->id)
            ->with('material')
            ->latest()
            ->get();
        return view('student.materials.index', compact('materials', 'student'));
    }
}
