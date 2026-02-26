<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StudentFee extends Model
{
    protected $table = 'student_fees';
    protected $fillable = [
        'student_id',
        'class_id',
        'amount',
        'payment_date',
        'payment_mode',
        'due_date',
        'status',
        'receipt_no',
        'slug',
        'is_active',
    ];

    protected $casts = ['payment_date' => 'date', 'due_date' => 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function classInfo()
    {
        return $this->belongsTo(Classes::class , 'class_id');
    }

    /** Auto-compute status from due_date vs payment_date */
    public static function computeStatus(string $dueDate, ?string $paymentDate): string
    {
        $due = Carbon::parse($dueDate);
        $late = $due->copy()->addDays(10);
        if ($paymentDate)
            return 'Paid';
        return now()->gt($late) ? 'Overdue' : 'Due';
    }
public static function dueInstallmentsCount(Student $student, ?Carbon $today = null): int
{
    if (!$student->joining_date) return 0;

    $today = ($today ?: now())->startOfDay();
    $join  = Carbon::parse($student->joining_date)->startOfDay();

    // First due date = join + 1 month (no overflow like Jan 31 -> Feb 28)
    $due = $join->copy()->addMonthNoOverflow();

    $count = 0;
    while ($due->lte($today)) {
        $count++;
        $due->addMonthNoOverflow();
        if ($count > 240) break; // safety
    }
    return $count;
}
public static function expectedTotalTillToday(Student $student): float
{
    $fee = (float) ($student->monthly_fees ?? 0);
    if ($fee <= 0) return 0;

    $installments = self::dueInstallmentsCount($student);
    return $installments * $fee;
}

public static function totalPaid(Student $student): float
{
    return (float) self::where('student_id', $student->id)->sum('amount');
}
    /** Calculate how many months have passed since join date (including partial month) */
    public static function getMonthsSinceJoin(Student $student): int
    {
        $joinDate = Carbon::parse($student->joining_date);
        $now = now();

        // Start from join month/year to now
        $months = $now->diffInMonths($joinDate->startOfMonth()) + 1;
        return max(1, $months);
    }

    /** Calculate the total balance due (Total Expected Fees - Total Paid) */
    public static function getTotalBalanceDue(Student $student): float
    {
        $monthsPassed = self::getMonthsSinceJoin($student);
        $totalExpected = $monthsPassed * ($student->monthly_fees ?? 0);
        $totalPaid = self::where('student_id', $student->id)->sum('amount');
        return max(0, $totalExpected - $totalPaid);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            $m->slug = $m->slug ?: \Str::slug('fee-' . uniqid());
            $m->receipt_no = $m->receipt_no ?: 'RCT-' . strtoupper(uniqid());
        });
    }
}
