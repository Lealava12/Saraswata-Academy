<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        return view('student.auth.login');
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

    public function showForgot()
    {
        return view('student.auth.forgot');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['mobile' => 'required']);
        $student = Student::where('mobile', $request->mobile)->first();
        if (!$student) {
            return back()->withErrors(['mobile' => 'Mobile number not found.']);
        }
        $otp = rand(100000, 999999);
        session(['student_forgot_otp' => $otp, 'student_forgot_mobile' => $request->mobile]);
        \Log::info("Student OTP for {$request->mobile}: {$otp}");
        return back()->with('success', "OTP sent! (Demo OTP: {$otp})");
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);
        if ($request->otp == session('student_forgot_otp')) {
            session(['student_otp_verified' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid OTP']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);
        if (!session('student_otp_verified')) {
            return back()->with('error', 'OTP not verified.');
        }
        $mobile = session('student_forgot_mobile');
        Student::where('mobile', $mobile)->update(['password' => Hash::make($request->password)]);
        session()->forget(['student_forgot_otp', 'student_forgot_mobile', 'student_otp_verified']);
        return redirect()->route('student.login')->with('success', 'Password reset successfully.');
    }
}
