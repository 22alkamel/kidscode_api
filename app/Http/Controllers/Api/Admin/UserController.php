<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::with(['studentProfile','trainerProfile'])->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:120',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|string|min:6',
            'role'=>'required|in:admin,trainer,student',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

         if ($request->hasFile('avatar')) {
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $data['avatar'] = $avatarPath;
        }

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

   

        // assign role using spatie
        $user->assignRole($data['role']);

        return response()->json(['message'=>'user_created','user'=>$user],201);
    }

    public function show(User $user)
    {
        return $user->load(['studentProfile','trainerProfile']);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'=>'nullable|string|max:120',
            'email'=>'nullable|email|unique:users,email,'.$user->id,
            'password'=>'nullable|string|min:6',
            'role'=>'nullable|in:admin,trainer,student',
            'status'=>'nullable|in:active,inactive,banned',
             'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        if (isset($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    }

    if ($request->hasFile('avatar')) {
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $data['avatar'] = $avatarPath;
    }

    $user->update($data);

    if (isset($data['role'])) {
        $user->syncRoles([$data['role']]);
    }

    return response()->json(['message'=>'user_updated','user'=>$user]);
    }

    

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message'=>'user_deleted']);
    }
}
