<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MpinController extends Controller
{
    /**
     * Check if a specific MPIN key is unlocked in the session.
     */
    public function status($key)
    {
        $isUnlocked = session()->has($key . '_unlocked') && session($key . '_unlocked') === true;
        return response()->json(['unlocked' => $isUnlocked]);
    }

    /**
     * Verify MPIN via AJAX and store in session.
     */
    public function verifyAjax(Request $request)
    {
        $request->validate([
            'mpin' => 'required',
            'key' => 'nullable|string'
        ]);

        $key = $request->key ?: 'mpin';
        $expectedMpin = config('app.admin_salary_mpin');

        if ($request->mpin === $expectedMpin) {
            session([$key . '_unlocked' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid MPIN'
        ]);
    }
}
