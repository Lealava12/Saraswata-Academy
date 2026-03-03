<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::latest()->get();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        if (!session('mpin_unlocked')) {
            return redirect()->route('admin.staff.index')->with('error', 'Authentication required.');
        }
        session()->forget('mpin_unlocked');
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:100',
            'mobile' => 'required|string|max:15',
            'monthly_salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
        ]);

        Staff::create([
            'name' => $request->name,
            'role' => $request->role,
            'mobile' => $request->mobile,
            'monthly_salary' => $request->monthly_salary,
            'joining_date' => $request->joining_date,
            'is_active' => $request->is_active ?? 1,
            'slug' => Str::slug($request->name . '-' . uniqid()),
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff created.');
    }

    public function edit(Staff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:100',
            'mobile' => 'required|string|max:15',
            'monthly_salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
        ]);

        $data = $request->only('name', 'role', 'mobile', 'monthly_salary', 'joining_date', 'is_active');
        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('success', 'Staff updated.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff removed.');
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
