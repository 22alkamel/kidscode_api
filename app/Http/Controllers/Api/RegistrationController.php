<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Registration;
use App\Helpers\ActivityLogger;
use App\Notifications\RegistrationStatusChanged;

class RegistrationController extends Controller
{
    // عرض جميع التسجيلات
    public function index()
    {
        $registrations = Registration::with(['user.studentProfile', 'program', 'payment'])->get();
        return response()->json($registrations);
    }

    // إنشاء تسجيل جديد
 public function store(Request $request)
{
    $request->validate([
        'program_id' => 'required|exists:programs,id',
        'preferred_days' => 'required|in:sat_tue,sun_wed,mon_thu',
        'preferred_time' => 'required|in:08-10,10-12,13-15,15-17,19-21',
    ]);

    $registration = Registration::create([
        'user_id' => $request->user()->id, // << هنا نأخذ id من المستخدم المصادق عليه
        'program_id' => $request->program_id,
        'preferred_days' => $request->preferred_days,
        'preferred_time' => $request->preferred_time,
        'status' => 'pending',
    ]);

    return response()->json($registration, 201);
}


    // عرض تسجيل واحد
    public function show($id)
    {
        $registration = Registration::with(['user', 'program', 'payment'])->findOrFail($id);
        return response()->json($registration);
    }

    // تحديث تسجيل
   public function update(Request $request, $id)
{
    $registration = Registration::findOrFail($id);
    $old = $registration->toArray();

    $registration->update($request->all());

    ActivityLogger::log(
        'registration_updated',
        'Registration',
        $registration->id,
        $old,
        $registration->toArray()
    );
    
    if ($old['status'] !== $registration->status) {
    $registration->user->notify(
        new RegistrationStatusChanged($registration)
    );
}

    return response()->json([
        'message' => 'Updated successfully',
        'data' => $registration
    ]);
}

    // حذف تسجيل
    public function destroy($id)
    {
        $registration = Registration::findOrFail($id);
        $registration->delete();

        return response()->json([
            'message' => 'Registration deleted successfully'
        ]);
    }

    // تسجيلات الطالب الحالي
public function myRegistrations(Request $request)
{
    $registrations = Registration::with('program')
        ->where('user_id', $request->user()->id)
        ->get();

    return response()->json([
        'programs_count' => $registrations->count(),
        'registrations' => $registrations
    ]);
}


}
