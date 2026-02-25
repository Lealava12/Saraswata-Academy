<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => 1])) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials or account disabled.'])->withInput();
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }

    public function showForgot()
    {
        return view('admin.auth.forgot-password');
    }
public function showVerifyOtp()
{
    // Check if email exists in session
    if (!session('reset_email')) {
        return redirect()->route('admin.forgot')->withErrors(['email' => 'Please request OTP first.']);
    }
    
    return view('admin.auth.verify-otp');
}
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // ✅ Change model/table if your admin table name is different
        $admin = \App\Models\Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'This email is not registered.']);
        }

        $otp = random_int(100000, 999999);

        // Store hashed OTP in password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        // Send mail
        Mail::raw(
            "Your OTP for password reset is: {$otp}\n\nThis OTP will expire in 10 minutes.",
            function ($m) use ($request) {
                $m->to($request->email)->subject('Password Reset OTP');
            }
        );

        // store email in session for next steps
        session([
            'reset_email' => $request->email,
            'otp_verified' => false
        ]);

        return redirect()->route('admin.verify.otp.form')->with('success', 'OTP sent to your email.');
    }

     public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|digits:6'
    ]);

    $email = session('reset_email');
    if (!$email) {
        return redirect()->route('admin.forgot')->withErrors(['email' => 'Session expired. Please try again.']);
    }

    $row = DB::table('password_reset_tokens')->where('email', $email)->first();

    if (!$row) {
        return back()->withErrors(['otp' => 'OTP not found. Please request again.']);
    }

    // Expiry check (10 minutes)
    if (Carbon::parse($row->created_at)->addMinutes(10)->isPast()) {
        return back()->withErrors(['otp' => 'OTP expired. Please request again.']);
    }

    // Match OTP
    if (!Hash::check($request->otp, $row->token)) {
        return back()->withErrors(['otp' => 'Invalid OTP.']);
    }

    session(['otp_verified' => true]);

    return redirect()->route('admin.reset.password.form')->with('success', 'OTP verified. Now reset password.');
}
public function showResetPassword()
{
    // security: only allow if otp verified
    if (session('otp_verified') !== true || !session('reset_email')) {
        return redirect()->route('admin.forgot')
            ->withErrors(['email' => 'Please verify OTP first.']);
    }

    return view('admin.auth.reset-password');
}
   public function resetPassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6|confirmed',
    ]);

    $email = session('reset_email');

    if (!$email || session('otp_verified') !== true) {
        return redirect()->route('admin.forgot')->withErrors(['email' => 'Please verify OTP first.']);
    }

    // ✅ Change model/table if needed
    \App\Models\Admin::where('email', $email)->update([
        'password' => Hash::make($request->password),
    ]);

    // delete token row
    DB::table('password_reset_tokens')->where('email', $email)->delete();

    // clear session
    session()->forget(['reset_email', 'otp_verified']);

    return redirect()->route('admin.login')->with('success', 'Password reset successfully. Please login.');
}
}
