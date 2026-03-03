<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class MpinManagementController extends Controller
{
    /**
     * Entry point for Manage MPIN
     */
    public function index()
    {
        // Forget any previous unlock session to force a fresh verification
        session()->forget('mpin_view_unlocked');

        return view('admin.mpin.request-otp');
    }

    /**
     * Generate and send OTP to the logged-in admin's email
     */
    public function sendOtp(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin || !$admin->email) {
            return back()->withErrors(['email' => 'Admin email not found.']);
        }

        $otp = random_int(100000, 999999);

        // Store hashed OTP in password_reset_tokens reusing the structure
        DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $admin->email],
        [
            'token' => Hash::make($otp),
            'created_at' => now(),
        ]
        );

        // Send mail
        Mail::raw(
            "Your OTP for viewing/editing your MPIN is: {$otp}\n\nThis OTP will expire in 10 minutes.",
            function ($m) use ($admin) {
            $m->to($admin->email)->subject('Manage MPIN OTP');
        }
        );

        // store email in session for next steps
        session([
            'mpin_otp_email' => $admin->email,
            'mpin_view_unlocked' => false
        ]);

        return redirect()->route('admin.manage-mpin.verify-otp.form')->with('success', 'OTP sent to your email.');
    }

    /**
     * Show OTP Verify Form
     */
    public function showVerifyOtp()
    {
        if (!session('mpin_otp_email')) {
            return redirect()->route('admin.manage-mpin')->withErrors(['otp' => 'Please request OTP first.']);
        }

        return view('admin.mpin.verify-otp');
    }

    /**
     * Verify the OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $email = session('mpin_otp_email');
        if (!$email) {
            return redirect()->route('admin.manage-mpin')->withErrors(['otp' => 'Session expired. Please try again.']);
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

        session(['mpin_view_unlocked' => true]);

        // Clean up token early if desired, or let it ride. Let's delete it.
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('admin.manage-mpin.view-edit')->with('success', 'OTP verified.');
    }

    /**
     * Show View/Edit MPIN form
     */
    public function viewEdit()
    {
        if (session('mpin_view_unlocked') !== true) {
            return redirect()->route('admin.manage-mpin')->withErrors(['otp' => 'Access denied. Please verify OTP first.']);
        }

        $currentMpin = config('app.admin_salary_mpin');

        return view('admin.mpin.view-edit', compact('currentMpin'));
    }

    /**
     * Update the MPIN in .env
     */
    public function update(Request $request)
    {
        if (session('mpin_view_unlocked') !== true) {
            return redirect()->route('admin.manage-mpin')->withErrors(['otp' => 'Access denied. Please verify OTP first.']);
        }

        $request->validate([
            'mpin' => 'required|digits:6',
        ]);

        $newMpin = $request->mpin;
        $this->setEnvValue('ADMIN_SALARY_MPIN', $newMpin);

        // Forget the unlock session after update so viewing it again requires a new OTP
        session()->forget('mpin_view_unlocked');

        return redirect()->route('admin.manage-mpin')->with('success', 'MPIN updated successfully.');
    }

    /**
     * Helper to update .env
     */
    private function setEnvValue($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $currentEnv = file_get_contents($path);

            // Try to match if the key exists
            $pattern = "/^{$key}=(.*)$/m";

            if (preg_match($pattern, $currentEnv)) {
                // Key exists, replace it
                $newEnv = preg_replace($pattern, "{$key}={$value}", $currentEnv);
            }
            else {
                // Key doesn't exist, append it
                $newEnv = $currentEnv . "\n{$key}={$value}\n";
            }

            file_put_contents($path, $newEnv);

        // Optional: clear config cache if needed, though config() shouldn't be cached in local dev.
        // \Artisan::call('config:clear');
        }
    }
}
