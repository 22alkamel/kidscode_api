<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentProfile;
use App\Models\TrainerProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $profile = $user->role === 'trainer' ? $user->trainerProfile : $user->studentProfile;
        return response()->json(['user' => $user, 'profile' => $profile]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'trainer') {
            $data = $request->validate([
                'bio' => 'nullable|string',
                'specialization' => 'nullable|string|max:120',
                'experience_years' => 'nullable|integer|min:0',
                'certifications' => 'nullable|array',
                'phone_number' => 'nullable|string|max:30',
                'whatsapp_number' => 'nullable|string|max:30',
                'age' => 'nullable|integer',
            ]);

            $profile = TrainerProfile::updateOrCreate(['user_id'=>$user->id], $data);

        } else {
            $data = $request->validate([
                'age' => 'nullable|integer',
                'school' => 'nullable|string|max:160',
                'grade' => 'nullable|string|max:40',
                'guardian_name' => 'nullable|string|max:120',
                'guardian_phone' => 'nullable|string|max:30',
                'interests' => 'nullable|array'
            ]);

            $profile = StudentProfile::updateOrCreate(['user_id'=>$user->id], $data);
        }

        return response()->json(['message'=>'profile_updated','profile'=>$profile]);
    }

    // 🔹 رفع الصورة
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = $request->user();

        $path = $request->file('avatar')->store('avatars','public');
        $user->avatar = "/storage/".$path;
        $user->save();

          return response()->json([
       'message'=>'avatar_uploaded',
       'avatar'=>$user->avatar
        ]);
    }

    // 🔹 تغيير كلمة المرور
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'=>'required|string',
            'new_password'=>'required|string|confirmed|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message'=>'كلمة المرور الحالية غير صحيحة'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message'=>'password_updated']);
    }
}
