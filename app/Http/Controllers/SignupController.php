<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function index()
    {
        return view('signUp');
    }

    public function signUp(Request $request)
    {
        // Validate request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);


        // Optionally delete OTP after successful signup
        Otp::where('email', $validatedData['email'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Signup successful!'
        ]);
    }

    public function sendOtp(Request $request)
    {
        $email = $request->email;
        $otp = rand(100000, 999999);

        info('Generated OTP: ' . $otp . ' for email: ' . $email);

        // Save or update the OTP in DB
        Otp::updateOrCreate(
            ['email' => $email],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(5)]
        );

        // Send OTP via email
        Mail::to($email)->send(new TestMail(['email' => $email, 'otp' => $otp]));

        return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
    }

    // New method to verify OTP
    public function verifyOtp(Request $request)
    {
        $email = $request->email;
        $otp = $request->otp;

        $record = Otp::where('email', $email)->first();

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'No OTP found for this email.']);
        }

        if ($record->expires_at < now()) {
            return response()->json(['success' => false, 'message' => 'OTP expired.']);
        }

        if ($record->otp != $otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP.']);
        }

        return response()->json(['success' => true, 'message' => 'OTP verified successfully']);
    }


    public function sendMail(Request $request)
    {
        $user = [
            'name' => $request->name ?? 'subhash',
            'email' => $request->email ?? 'subhashmagharola@example.com'
        ];

        Mail::to($user['email'])->send(new TestMail($user));

        return "Mail sent successfully!";
    }
}
