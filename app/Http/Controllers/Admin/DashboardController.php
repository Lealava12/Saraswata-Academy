<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\StudentFee;
use App\Models\Expenditure;
use App\Models\TeacherSalary;
use App\Models\StaffSalary;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalStaff = Staff::count();

        $currentMonth = Carbon::now()->format('Y-m');
        $feeCollected = StudentFee::where('status', 'Paid')
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $feeDue = StudentFee::where('status', 'Due')->sum('amount');
        $feeOverdue = StudentFee::where('status', 'Overdue')->sum('amount');
        $overdueCount = StudentFee::where('status', 'Overdue')->count();

        // Monthly fee income vs expenses
        $totalExpenditure = Expenditure::whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        $teacherPaid = TeacherSalary::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $staffPaid = StaffSalary::whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $recentStudents = Student::with(['classInfo', 'board'])
            ->latest()
            ->take(5)
            ->get();

        $overdueStudents = StudentFee::with('student')
            ->where('status', 'Overdue')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalStudents',
            'totalTeachers',
            'totalStaff',
            'feeCollected',
            'feeDue',
            'feeOverdue',
            'overdueCount',
            'totalExpenditure',
            'teacherPaid',
            'staffPaid',
            'recentStudents',
            'overdueStudents'
        ));
    }
}
