<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Student;

// Root redirect
Route::get('/', fn() => redirect()->route('admin.login'));

Route::get('/mail-test', function () {
    \Illuminate\Support\Facades\Mail::raw('Mail test from Saraswata Academy', function ($m) {
            $m->to('alamkhank2015@gmail.com')->subject('SMTP Test');
        }
        );
        return 'sent';    });

// ─── ADMIN ROUTES ────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth (public)
    Route::get('login', [Admin\AuthController::class , 'showLogin'])->name('login');
    Route::post('login', [Admin\AuthController::class , 'login'])->name('login.post');
    Route::get('logout', [Admin\AuthController::class , 'logout'])->name('logout');

    Route::get('forgot-password', [Admin\AuthController::class , 'showForgot'])->name('forgot');
    Route::post('forgot-password', [Admin\AuthController::class , 'sendOtp'])->name('forgot.otp');
    Route::get('verify-otp', [Admin\AuthController::class , 'showVerifyOtp'])->name('verify.otp.form');
    Route::post('verify-otp', [Admin\AuthController::class , 'verifyOtp'])->name('verify.otp');
    Route::get('reset-password', [Admin\AuthController::class , 'showResetPassword'])->name('reset.password.form');
    Route::post('reset-password', [Admin\AuthController::class , 'resetPassword'])->name('reset.password');

    // Authenticated routes
    Route::middleware('admin.auth')->group(function () {

            // Dashboard
            Route::get('dashboard', [Admin\DashboardController::class , 'index'])->name('dashboard');

            // Profile
            Route::get('profile', [Admin\ProfileController::class , 'edit'])->name('profile.edit');
            Route::put('profile', [Admin\ProfileController::class , 'update'])->name('profile.update');

            // Admin Accounts
            Route::resource('admins', Admin\AdminController::class)->except(['show']);

            // Boards
            Route::resource('boards', Admin\BoardController::class)->except(['show']);

            // Classes
            Route::resource('classes', Admin\ClassController::class)->except(['show']);

            // Subjects
            Route::resource('subjects', Admin\SubjectController::class)->except(['show']);

            // Students
            Route::resource('students', Admin\StudentController::class);
            Route::get('students/{student}/fee-history', [Admin\StudentController::class , 'feeHistory'])->name('students.fee-history');

            // Teachers
            Route::resource('teachers', Admin\TeacherController::class)->except(['show']);
            Route::get('teachers/verify-mpin', [Admin\TeacherController::class , 'verifyMpin'])->name('teachers.verify-mpin');
            Route::get('teachers/{teacher}', [Admin\TeacherController::class , 'show'])->name('teachers.show');

            // Teacher Salary
            Route::resource('teacher-salary', Admin\TeacherSalaryController::class)->only(['index', 'create', 'store', 'destroy', 'show']);
            Route::post('teacher-salary/verify-mpin', [Admin\TeacherSalaryController::class , 'verifyMpin'])->name('teacher-salary.verify-mpin');

            // Staff
            Route::resource('staff', Admin\StaffController::class)->except(['show']);

            // Staff Salary
            Route::resource('staff-salary', Admin\StaffSalaryController::class)->only(['index', 'create', 'store', 'destroy']);
            Route::post('staff-salary/verify-mpin', [Admin\StaffSalaryController::class , 'verifyMpin'])->name('staff-salary.verify-mpin');

            // Expenditures
            Route::resource('expenditures', Admin\ExpenditureController::class)->except(['show']);

            // Attendance
            Route::get('attendance', [Admin\AttendanceController::class , 'index'])->name('attendance.index');
            Route::get('attendance/create', [Admin\AttendanceController::class , 'create'])->name('attendance.create');
            Route::post('attendance', [Admin\AttendanceController::class , 'store'])->name('attendance.store');
            Route::get('attendance/{id}', [Admin\AttendanceController::class , 'show'])->name('attendance.show');
            Route::post('attendance/get-students', [Admin\AttendanceController::class , 'getStudents'])->name('attendance.get-students');

            // Exams & Marks
            Route::resource('exams', Admin\ExamController::class)->except(['show']);
            Route::get('exams/{exam}/marks', [Admin\ExamController::class , 'marks'])->name('exams.marks');
            Route::post('exams/{exam}/marks', [Admin\ExamController::class , 'storeMarks'])->name('exams.marks.store');
            Route::get('exams/export-pdf', [Admin\ExamController::class , 'exportPdf'])->name('exams.export-pdf');
            Route::get('exams/{exam}/view-marks', [Admin\ExamController::class , 'viewMarks'])->name('exams.view-marks');
            Route::get('exams/{exam}/marks-export-pdf', [Admin\ExamController::class , 'exportMarksPdf'])->name('exams.marks.export-pdf');
            Route::get('exams/export-csv', [Admin\ExamController::class , 'exportCsv'])->name('exams.export-csv');
            Route::get('exams/{exam}/marks-export-csv', [Admin\ExamController::class , 'exportMarksCsv'])->name('exams.marks.export-csv');

            // Fees
            Route::get('fees', [Admin\FeeController::class , 'index'])->name('fees.index');
            Route::get('fees/create', [Admin\FeeController::class , 'create'])->name('fees.create');
            Route::post('fees', [Admin\FeeController::class , 'store'])->name('fees.store');
            Route::post('fees/verify-mpin', [Admin\FeeController::class , 'verifyMpin'])->name('fees.verify-mpin');
            Route::get('fees/students-by-class', [Admin\AjaxFeeController::class , 'getStudentsByClass'])->name('fees.students-by-class');

            // Study Material
            Route::resource('study-materials', Admin\StudyMaterialController::class)->except(['show']);
            Route::get('study-materials/{material}/assign', [Admin\StudyMaterialController::class , 'assign'])->name('study-materials.assign');
            Route::post('study-materials/{material}/assign', [Admin\StudyMaterialController::class , 'doAssign'])->name('study-materials.do-assign');
            Route::get('study-materials/{material}/assignments', [Admin\StudyMaterialController::class , 'viewAssignments'])->name('study-materials.assignments');
            Route::put('study-materials/assignment/{assignment}/update-status', [Admin\StudyMaterialController::class , 'updateStatus'])->name('study-materials.update-status');

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                    Route::get('attendance', [Admin\ReportController::class , 'attendance'])->name('attendance');
                    Route::post('get-students-by-class', [Admin\ReportController::class , 'getStudentsByClass'])->name('get-students-by-class');
                    Route::get('export-attendance-csv', [Admin\ReportController::class , 'exportAttendanceCsv'])->name('export-attendance-csv');
                    Route::get('export-attendance-pdf', [Admin\ReportController::class , 'exportAttendancePdf'])->name('export-attendance-pdf');
                    Route::get('exam', [Admin\ReportController::class , 'exam'])->name('exam');
                    Route::get('fee', [Admin\ReportController::class , 'fee'])->name('fee');
                    Route::get('financial', [Admin\ReportController::class , 'financial'])->name('financial');
                    Route::get('export-pdf', [Admin\ReportController::class , 'exportPdf'])->name('export-pdf');
                    Route::get('export-csv', [Admin\ReportController::class , 'exportCsv'])->name('export-csv');
                }
                );
            }
            );        });

// ─── STUDENT ROUTES ───────────────────────────────────────────────────────────
Route::prefix('student')->name('student.')->group(function () {

    // Auth (public)
    Route::get('login', [Student\AuthController::class , 'showLogin'])->name('login');
    Route::post('login', [Student\AuthController::class , 'login'])->name('login.post');
    Route::get('logout', [Student\AuthController::class , 'logout'])->name('logout');
    Route::get('forgot-password', [Student\AuthController::class , 'showForgot'])->name('forgot');
    Route::post('forgot-password', [Student\AuthController::class , 'sendOtp'])->name('forgot.otp');
    Route::get('verify-otp', [Student\AuthController::class , 'showVerifyOtp'])->name('verify.otp.form');
    Route::post('verify-otp', [Student\AuthController::class , 'verifyOtp'])->name('verify.otp');
    Route::get('reset-password', [Student\AuthController::class , 'showResetPassword'])->name('reset.password.form');
    Route::post('reset-password', [Student\AuthController::class , 'resetPassword'])->name('reset.password');

    // Authenticated routes
    Route::middleware('student.auth')->group(function () {
            Route::get('dashboard', [Student\DashboardController::class , 'index'])->name('dashboard');
            Route::get('fees', [Student\FeeController::class , 'index'])->name('fees');
            Route::get('attendance', [Student\AttendanceController::class , 'index'])->name('attendance');
            Route::get('exams', [Student\ExamController::class , 'index'])->name('exams');
            Route::get('materials', [Student\MaterialController::class , 'index'])->name('materials');
        }
        );    });