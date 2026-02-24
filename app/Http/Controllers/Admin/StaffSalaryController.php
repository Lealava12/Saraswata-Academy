<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffSalary;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffSalaryController extends Controller
{
    public function index()
    {
        $salaries = StaffSalary::with('staff')->latest()->get();
        return view('admin.staff-salary.index', compact('salaries'));
    }

    public function create()
    {
        if (!session('mpin_unlocked')) {
            return redirect()->route('admin.staff-salary.index')->with('error', 'Authentication required.');
        }
        session()->forget('mpin_unlocked'); // Force re-auth for next enter
        $staff = Staff::where('is_active', 1)->get();
        return view('admin.staff-salary.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'payment_month' => 'required|string',
            'payment_date' => 'required|date',
        ]);

        StaffSalary::create([
            'staff_id' => $request->staff_id,
            'amount' => $request->amount,
            'payment_month' => $request->payment_month,
            'payment_date' => $request->payment_date,
            'slug' => Str::slug('ssal-' . uniqid()),
        ]);

        return redirect()->route('admin.staff-salary.index')->with('success', 'Salary recorded.');
    }

    public function destroy(StaffSalary $staffSalary)
    {
        $staffSalary->delete();
        return redirect()->route('admin.staff-salary.index')->with('success', 'Record removed.');
    }

    public function verifyMpin(Request $request)
    {
        if ($request->mpin === env('ADMIN_SALARY_MPIN')) {
            session(['mpin_unlocked' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid MPIN']);
    }
}
