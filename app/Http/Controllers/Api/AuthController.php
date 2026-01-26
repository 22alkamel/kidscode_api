<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class AuthController extends Controller
{
    // ---------------------------------------
    // 1) Register
    // ---------------------------------------

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'nullable|in:student,trainer,admin',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => bcrypt($data['password']),
            'role'      => $data['role'] ?? 'student',
        ]);

        // -------- Generate OTP --------
        $otp = rand(100000, 999999);

        $user->otp_code = $otp;
        $user->otpexpiresat = Carbon::now()->addMinutes(10);
        $user->otp_verified = false;
        $user->save();

        // TODO: إرسال OTP عبر البريد أو واتساب لاحقًا
        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json([
            'message' => 'registered_successfully',
            'otp_sent' => true,
            'email' => $user->email,
        ], 201);
    }


        // ---------------------------------------
    // 2) Verify OTP
    // ---------------------------------------

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'otp'     => 'required',
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        if (!$user->otp_code || $user->otp_code != $data['otp']) {
            return response()->json(['message' => 'invalid_otp'], 422);
        }

        if ($user->otpexpiresat->isPast()) {
            return response()->json(['message' => 'otp_expired'], 422);
        }

        // ✔️ تفعيل الحساب
        $user->otp_verified = true;
        $user->email_verified = true;
        $user->emailverifiedat = now();

        // مسح otp بعد التفعيل
        $user->otp_code = null;
        $user->otpexpiresat = null;
        $user->save();

        // أصنعي token لتسجيل الدخول
        $token = $user->createToken("api-token")->plainTextToken;

        return response()->json([
            'message' => 'otp_verified',
            'token'   => $token,
            'user'    => $user,
        ]);
    }



        // ---------------------------------------
    // 3) Resend OTP
    // ---------------------------------------

    public function resendOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        // إنشاء OTP جديد
        $otp = rand(100000, 999999);

        $user->otp_code = $otp;
        $user->otpexpiresat = Carbon::now()->addMinutes(10);
        $user->otp_verified = false;
        $user->save();

        // TODO: إرسال OTP هنا
        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json([
            'message' => 'otp_resent',
            'otp_sent' => true,
        ]);
    }


        // ---------------------------------------
    // 4) Login
    // ---------------------------------------

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'invalid_credentials'], 401);
        }

        if (!$user->otp_verified) {
            return response()->json(['message' => 'otp_not_verified'], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'logged_in',
            'token' => $token,
            'user' => $user,
        ]);
    }


    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'logout_success']);
    }



    /**
     * GET ME (Profile)
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }


}