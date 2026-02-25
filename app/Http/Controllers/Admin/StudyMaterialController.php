<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyMaterial;
use App\Models\StudentMaterial;
use App\Models\Student;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StudyMaterialController extends Controller
{
   public function index()
{
    $materials = StudyMaterial::withCount(['studentMaterials' => function ($query) {
            $query->where('status', 'Issued'); // Counts only "Issued" assignments
        }])
        ->whereHas('studentMaterials', function ($query) {
            $query->where('status', 'Issued'); // Only shows materials that have at least one "Issued" assignment
        })
        ->latest()
        ->get();

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
            'is_active' => 'required|in:0,1',
        ]);

        StudyMaterial::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active,
            'slug' => Str::slug($request->name . '-' . uniqid()),
        ]);

        return redirect()->route('admin.study-materials.index')
            ->with('success', 'Material created successfully.');
    }

    public function edit(StudyMaterial $study_material) // resource route param name depends on resource; keep safe
    {
        // To keep blades consistent, pass as $material
        $material = $study_material;
        return view('admin.study-materials.edit', compact('material'));
    }

    public function update(Request $request, StudyMaterial $study_material)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|in:0,1',
        ]);

        $study_material->update($request->only('name', 'description', 'is_active'));

        return redirect()->route('admin.study-materials.index')
            ->with('success', 'Material updated successfully.');
    }

    public function destroy(StudyMaterial $study_material)
    {
        if ($study_material->studentMaterials()->count() > 0) {
            return back()->with('error', 'Cannot delete material as it is assigned to students.');
        }

        $study_material->delete();

        return redirect()->route('admin.study-materials.index')
            ->with('success', 'Material removed successfully.');
    }

    // ✅ Dropdown Assign Page
   public function assign(StudyMaterial $material)
    {
        if (!$material->is_active) {
            return redirect()->route('admin.study-materials.index')
                ->with('error', 'This material is inactive and cannot be assigned.');
        }

        $classes = Classes::where('is_active', 1)->get();

        $students = Student::where('is_active', 1)
            ->with('classInfo')
            ->orderBy('class_id')
            ->orderBy('roll_no')
            ->get();

        return view('admin.study-materials.assign', compact('material', 'students', 'classes'));
    }

    // ✅ Assign 1 student using dropdown
    public function doAssign(Request $request, StudyMaterial $material)
{
    if (!$material->is_active) {
        return back()->with('error', 'This material is inactive and cannot be assigned.');
    }

    $request->validate([
        'student_id' => 'required|exists:students,id',
        'issue_date' => 'required|date',
        'status' => "required|in:Issued,Not Issued",
    ]);

    StudentMaterial::updateOrCreate(
        [
            'student_id' => $request->student_id,
            'study_material_id' => $material->id,
        ],
        [
            'issue_date' => $request->issue_date,
            'status' => $request->status,
            'slug' => Str::slug('sm-' . uniqid() . '-' . $request->student_id),
        ]
    );

    return redirect()->route('admin.study-materials.assignments', ['material' => $material->id])
        ->with('success', 'Material status saved successfully.');
}

    public function viewAssignments(StudyMaterial $material)
    {
        $assignments = StudentMaterial::with(['student.classInfo'])
            ->where('study_material_id', $material->id)
            ->latest()
            ->get();

        return view('admin.study-materials.assignments', compact('material', 'assignments'));
    }

    public function updateStatus(Request $request, StudentMaterial $assignment)
    {
        $request->validate([
            'status' => 'required|in:Issued,Returned',
            'return_date' => 'nullable|date|required_if:status,Returned',
        ]);

        $assignment->update([
            'status' => $request->status,
            'return_date' => $request->status === 'Returned' ? $request->return_date : null,
        ]);

        return back()->with('success', 'Assignment status updated successfully.');
    }
}