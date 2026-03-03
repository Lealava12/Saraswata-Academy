<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class Student extends Authenticatable
{
    use Notifiable;

    protected $guard = 'student';

    protected $fillable = [
        'student_id',
        'roll_no',
        'name',
        'email',
        'mobile',
        'password',
        'dob',
        'school_name',
        'class_id',
        'board_id',
        'photo',
        'progress_report',
        'monthly_fees',
        'joining_date',
        'slug',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function classInfo()
    {
        return $this->belongsTo(Classes::class , 'class_id');
    }
    public function board()
    {
        return $this->belongsTo(Board::class,'board_id');
    }
    public function parent()
    {
        return $this->hasOne(StudentParent::class);
    }
    public function fees()
    {
        return $this->hasMany(StudentFee::class);
    }
    public function attendances()
    {
        return $this->hasMany(AttendanceDetail::class);
    }
    public function examMarks()
    {
        return $this->hasMany(ExamMark::class);
    }
    public function materials()
    {
        return $this->hasMany(StudentMaterial::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->slug = $m->slug ?: \Str::slug($m->name . '-' . uniqid()));
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', 1);
    }
public function getTotalPaidFees(): float
{
    return (float) $this->fees()->sum('amount');
}

/**
 * Expected fees till today:
 * First due = joining_date + 1 month (no overflow)
 */
public function getExpectedFeesTillToday(): float
{
    $fee = (float)($this->monthly_fees ?? 0);
    if ($fee <= 0 || !$this->joining_date) return 0;

    $today = now()->startOfDay();
    $join  = Carbon::parse($this->joining_date)->startOfDay();

    $due = $join->copy()->addMonthNoOverflow();

    $count = 0;
    while ($due->lte($today)) {
        $count++;
        $due->addMonthNoOverflow();
        if ($count > 240) break; // safety
    }

    return $count * $fee;
}

public function getBalanceDue(): float
{
    $expected = $this->getExpectedFeesTillToday();
    $paid = $this->getTotalPaidFees();
    return max(0, $expected - $paid);
}

/**
 * Overdue means pending balance exists AND last due date +10 days passed
 */
public function getOverdueStatus(): bool
{
    if (!$this->joining_date) return false;

    $fee = (float)($this->monthly_fees ?? 0);
    if ($fee <= 0) return false;

    $balance = $this->getBalanceDue();
    if ($balance <= 0) return false;

    $today = now()->startOfDay();
    $join  = Carbon::parse($this->joining_date)->startOfDay();

    // find latest due date that is <= today (due starts after 1 month)
    $due = $join->copy()->addMonthNoOverflow();
    $lastDue = null;

    while ($due->lte($today)) {
        $lastDue = $due->copy();
        $due->addMonthNoOverflow();
        if ($due->diffInMonths($join) > 240) break;
    }

    if (!$lastDue) return false; // no due yet

    return $today->gt($lastDue->copy()->addDays(10));
}
    /**
     * Generate the next student_id for the given academic session year.
     * Format: SA + yearCode (e.g. "2526") + 4-digit seq
     */
    public static function generateStudentId(): string
    {
        $now = now();
        $month = (int)$now->format('n');
        // Academic year April–March (adjusted for India March start per flowchart)
        // If month >= 3 (March) the session is current year → next year
        $startYear = $month >= 3 ? $now->year : $now->year - 1;
        $endYear = $startYear + 1;
        $code = substr($startYear, 2, 2) . substr($endYear, 2, 2); // e.g. 2526
        $prefix = 'SA' . $code;

        $last = static::where('student_id', 'like', $prefix . '%')
            ->orderByDesc('id')->first();
        $seq = $last ? ((int)substr($last->student_id, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
