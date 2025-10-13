<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        //UNCOMMENT BELOW TO ADD OTP
         $user = \App\Models\User::where('email', $request->email)->first();

         if (! $user || ! \Hash::check($request->password, $user->password)) {
             throw \Illuminate\Validation\ValidationException::withMessages([
                 'email' => __('auth.failed'),
             ]);
         }

         if (! $user->hasVerifiedEmail()) {
             $user->sendEmailVerificationNotification();

             return back()->withErrors([
                 'email' => 'Please verify your email address. A new verification link has been sent to your email.',
             ]);
         }

         if ($user->status !== 'active') {
             return back()->withErrors(['email' => 'Your account is not active. Please wait for admin approval.']);
         }

         $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
         $otp = '';
         for ($i = 0; $i < 6; $i++) {
             $otp .= $characters[rand(0, strlen($characters) - 1)];
         }

         // You can create an OTP model or just store it in session for simplicity
         session(['otp_email' => $user->email, 'otp_code' => $otp, 'otp_expires_at' => now()->addMinutes(5)]);

         // Send the OTP via email
         \Mail::to($user->email)->send(new \App\Mail\SendOtpMail($otp));

         // auto logout after otp is sent
        Auth::logoutOtherDevices($request->password);

         return redirect()->route('verify.otp.form')->with('status', 'An OTP has been sent to your email.');

        //COMMENT BELOW TO REMOVE OTP
//        $request->authenticate();
//        $request->session()->regenerate();
//        Auth::logoutOtherDevices($request->password);
//
//        if (auth()->user()->hasRole('encoder')) {
//            return redirect()->route('child.index');
//        }
//
//        return redirect()->route('child.index');
        // END OTP COMMENT
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
