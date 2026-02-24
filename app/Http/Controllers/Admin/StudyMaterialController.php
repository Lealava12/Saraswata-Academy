<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyMaterial;
use App\Models\StudentMaterial;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudyMaterialController extends Controller
{
    public function index()
    {
        $materials = StudyMaterial::latest()->get();
        return view('admin.study-materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.study-materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        StudyMaterial::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? 1,
            'slug' => Str::slug($request->name . '-' . uniqid()),
        ]);
        return redirect()->route('admin.study-materials.index')->with('success', 'Material created.');
    }

    public function edit(StudyMaterial $studyMaterial)
    {
        return view('admin.study-materials.edit', compact('studyMaterial'));
    }

    public function update(Request $request, StudyMaterial $studyMaterial)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $studyMaterial->update($request->only('name', 'description', 'is_active'));
        return redirect()->route('admin.study-materials.index')->with('success', 'Material updated.');
    }

    public function destroy(StudyMaterial $studyMaterial)
    {
        $studyMaterial->delete();
        return redirect()->route('admin.study-materials.index')->with('success', 'Material removed.');
    }

    public function assign(StudyMaterial $material)
    {
        $students = Student::where('is_active', 1)->with('classInfo')->get();
        $assigned = StudentMaterial::where('material_id', $material->id)->pluck('student_id')->toArray();
        return view('admin.study-materials.assign', compact('material', 'students', 'assigned'));
    }

    public function doAssign(Request $request, StudyMaterial $material)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'issue_date' => 'required|date',
        ]);

        StudentMaterial::updateOrCreate(
            ['student_id' => $request->student_id, 'material_id' => $material->id],
            [
                'issue_date' => $request->issue_date,
                'status' => 'Issued',
                'slug' => Str::slug('sm-' . uniqid()),
            ]
        );

        return redirect()->route('admin.study-materials.index')->with('success', 'Material assigned.');
    }
}
