<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureOtpVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->otp_verified) {
            return response()->json([
                'message' => 'otp_not_verified'
            ], 403);
        }

        return $next($request);
    }
}
