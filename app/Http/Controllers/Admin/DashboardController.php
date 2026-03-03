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

       $feeDue = 0;
$feeOverdue = 0;
$overdueCount = 0;

$students = Student::where('is_active', 1)->with('fees')->get();

foreach ($students as $s) {
    $balance = $s->getBalanceDue();  // you already use this in pending page
    if ($balance > 0) {
        $feeDue += $balance;
        if ($s->getOverdueStatus()) {
            $feeOverdue += $balance;
            $overdueCount++;
        }
    }
}
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

        // Late payment alerts (> 10 days overdue)
        $latePaymentAlerts = Student::where('is_active', 1)
            ->get()
            ->filter(fn($s) => $s->getOverdueStatus())
            ->take(5);

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
            'overdueStudents',
            'latePaymentAlerts'
        ));
    }
}
