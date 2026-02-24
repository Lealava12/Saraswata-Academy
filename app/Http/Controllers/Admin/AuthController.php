<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

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
        return view('admin.auth.forgot');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['mobile' => 'required']);
        $admin = Admin::where('mobile', $request->mobile)->first();
        if (!$admin) {
            return back()->withErrors(['mobile' => 'Mobile number not found.']);
        }
        $otp = rand(100000, 999999);
        session(['admin_forgot_otp' => $otp, 'admin_forgot_mobile' => $request->mobile]);
        // In production: send OTP via SMS. For now log it.
        \Log::info("Admin OTP for {$request->mobile}: {$otp}");
        return back()->with('success', "OTP sent! (Demo OTP: {$otp})");
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);
        if ($request->otp == session('admin_forgot_otp')) {
            session(['admin_otp_verified' => true]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Invalid OTP']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);
        if (!session('admin_otp_verified')) {
            return back()->with('error', 'OTP not verified.');
        }
        $mobile = session('admin_forgot_mobile');
        Admin::where('mobile', $mobile)->update(['password' => Hash::make($request->password)]);
        session()->forget(['admin_forgot_otp', 'admin_forgot_mobile', 'admin_otp_verified']);
        return redirect()->route('admin.login')->with('success', 'Password reset successfully.');
    }
}
