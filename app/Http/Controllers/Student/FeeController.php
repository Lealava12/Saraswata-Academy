<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentFee;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::guard('student')->user();

        $query = StudentFee::where('student_id', $student->id);

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('payment_date', [$request->from_date, $request->to_date]);
        }

        $fees = $query->with('classInfo')->latest()->get();

        $totalPaid = $fees->where('status', 'Paid')->sum('amount');
        $totalDue = $fees->where('status', 'Due')->sum('amount');
        $totalOverdue = $fees->where('status', 'Overdue')->sum('amount');

        return view('student.fees.index', compact('fees', 'totalPaid', 'totalDue', 'totalOverdue', 'student'));
    }
}
