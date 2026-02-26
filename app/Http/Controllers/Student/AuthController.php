<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        return view('student.auth.login');
    }
    public function showForgot()
    {
        return view('student.auth.forgot-password');
    }
    public function showVerifyOtp()
    {
        if (!session('student_reset_email')) {
            return redirect()->route('student.forgot')
                ->withErrors(['email' => 'Please request OTP first.']);
        }

        return view('student.auth.verify-otp');
    }
    public function showResetPassword()
    {
        if (session('student_otp_verified') !== true || !session('student_reset_email')) {
            return redirect()->route('student.forgot')
                ->withErrors(['email' => 'Please verify OTP first.']);
        }

        return view('student.auth.reset-password');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        // Try student_id first, then email
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';
        $student = Student::where($field, $request->login)
            ->where('is_active', 1)->first();

        if ($student && Hash::check($request->password, $student->password)) {
            Auth::guard('student')->login($student);
            return redirect()->route('student.dashboard');
        }

        return back()->withErrors(['login' => 'Invalid credentials or account disabled.'])->withInput();
    }

    public function logout()
    {
        Auth::guard('student')->logout();
        return redirect()->route('student.login')->with('success', 'Logged out successfully.');
    }



    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $student = Student::where('email', $request->email)
            ->where('is_active', 1)
            ->first();

        if (!$student) {
            return back()->withErrors(['email' => 'Email not found or account disabled.']);
        }

        $otp = random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($otp), 'created_at' => now()]
        );

        Mail::raw(
            "Your OTP for student password reset is: {$otp}\n\nThis OTP will expire in 10 minutes.",
            function ($m) use ($request) {
                $m->to($request->email)->subject('Student Password Reset OTP');
            }
        );

        session([
            'student_reset_email' => $request->email,
            'student_otp_verified' => false,
        ]);

        return redirect()->route('student.verify.otp.form')->with('success', 'OTP sent to your email.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $email = session('student_reset_email');
        if (!$email) {
            return redirect()->route('student.forgot')
                ->withErrors(['email' => 'Session expired. Please request OTP again.']);
        }

        $row = DB::table('password_reset_tokens')->where('email', $email)->first();
        if (!$row) {
            return back()->withErrors(['otp' => 'OTP not found. Please request again.']);
        }

        if (Carbon::parse($row->created_at)->addMinutes(10)->isPast()) {
            return back()->withErrors(['otp' => 'OTP expired. Please request again.']);
        }

        if (!Hash::check($request->otp, $row->token)) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        session(['student_otp_verified' => true]);

        return redirect()->route('student.reset.password.form')->with('success', 'OTP verified. Now reset password.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $email = session('student_reset_email');

        if (!$email || session('student_otp_verified') !== true) {
            return redirect()->route('student.forgot')
                ->withErrors(['email' => 'Please verify OTP first.']);
        }

        Student::where('email', $email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        session()->forget(['student_reset_email', 'student_otp_verified']);

        return redirect()->route('student.login')->with('success', 'Password reset successfully. Please login.');
    }
}
