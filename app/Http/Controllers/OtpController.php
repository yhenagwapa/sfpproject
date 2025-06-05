<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function showOtpForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $email = session('otp_email');
        $storedOtp = session('otp_code');
        $expiresAt = session('otp_expires_at');

        if (!$email || !$storedOtp || !$expiresAt || now()->gt($expiresAt)) {
            return back()->withErrors(['otp' => 'OTP expired or invalid.']);
        }

        if ($request->otp != $storedOtp) {
            return back()->withErrors(['otp' => 'Incorrect OTP.']);
        }

        // All good: log in the user
        $user = User::where('email', $email)->first();
        Auth::login($user);

        // Clear session OTP
        session()->forget(['otp_email', 'otp_code', 'otp_expires_at']);

        return redirect()->route('child.index');
    }
}
