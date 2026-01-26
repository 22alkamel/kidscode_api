<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\ProgramEnrollment;

class EnrollmentController extends Controller
{
    // ----------------------------
    // Enroll student
    // ----------------------------
    public function enroll(Request $request, Program $program)
    {
        $user = $request->user();

        $data = $request->validate([
            'age_group' => 'required|string',
            'payment_method' => 'required|in:whatsapp,sms,cash,other',
            'payment_reference' => 'nullable|string',
        ]);

        // منع تكرار التسجيل
        if (ProgramEnrollment::where('program_id', $program->id)
            ->where('user_id', $user->id)
            ->exists()) {
            return response()->json(['message' => 'already_enrolled'], 409);
        }

        $enroll = ProgramEnrollment::create([
            'program_id' => $program->id,
            'user_id' => $user->id,
            'age_group' => $data['age_group'],
            'payment_method' => $data['payment_method'],
            'payment_reference' => $data['payment_reference'],
            'payment_status' => 'pending',
            'enrolled_at' => now(),
        ]);

        return response()->json([
            'message' => 'enrolled_successfully',
            'enrollment' => $enroll
        ], 201);
    }


    // ----------------------------
    // Admin Confirm Payment
    // ----------------------------
    public function confirmPayment(Request $request, ProgramEnrollment $enrollment)
    {
        $data = $request->validate([
            'status' => 'required|in:confirmed,rejected',
            'confirmation_note' => 'nullable|string',
        ]);

        $enrollment->update([
            'payment_status' => $data['status'],
            'confirmation_note' => $data['confirmation_note'],
            'confirmed_by' => $request->user()->id,
            'confirmed_at' => now(),
            'activated' => $data['status'] === 'confirmed',
            'activation_at' => $data['status'] === 'confirmed' ? now() : null,
        ]);

        return response()->json([
            'message' => 'payment_updated',
            'enrollment' => $enrollment
        ]);
    }


///////////////////////////////////////////

    public function cancel(ProgramEnrollment $enrollment)
{
    if ($enrollment->user_id !== auth()->id()) {
        return response()->json(['message' => 'not_allowed'], 403);
    }

    $enrollment->delete();

    return response()->json(['message' => 'enrollment_canceled']);
}

public function myEnrollments()
{
    return auth()->user()->enrollments()->with('program')->get();
}


public function programEnrollments(Program $program)
{
    return $program->enrollments()->with('user')->paginate(20);
}


}
