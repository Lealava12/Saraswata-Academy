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
        return $this->belongsTo(Classes::class, 'class_id');
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

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            $m->slug = $m->slug ?: \Str::slug('fee-' . uniqid());
            $m->receipt_no = $m->receipt_no ?: 'RCT-' . strtoupper(uniqid());
        });
    }
}
