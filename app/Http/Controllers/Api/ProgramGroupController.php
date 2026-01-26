<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProgramGroup;
use App\Models\GroupStudent;
use App\Models\Registration;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class ProgramGroupController extends Controller
{
    // ===== جلب جميع الجروبات داخل برنامج معين =====
    public function index(Program $program)
    {
        return ProgramGroup::where('program_id', $program->id)
            ->with(['trainer', 'students'])
            ->get();
    }

    // ===== إنشاء جروب جديد داخل برنامج =====
    public function store(Request $request, Program $program)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'trainer_id' => 'required|exists:users,id'
        ]);

        $trainer = User::find($data['trainer_id']);
        if ($trainer->role !== 'trainer') {
            return response()->json(['message' => 'invalid_trainer_role'], 422);
        }

        $group = ProgramGroup::create([
            'program_id' => $program->id,
            'name' => $data['name'],
            'trainer_id' => $data['trainer_id'],
        ]);

        return response()->json([
            'message' => 'group_created',
            'group' => $group
        ], 201);
    }

    // ===== تعديل الجروب =====
    public function update(Request $request, ProgramGroup $group)
    {
        $this->authorize('access-group', $group);

        $data = $request->validate([
            'name' => 'string|max:120',
            'trainer_id' => 'exists:users,id',
        ]);

        if (isset($data['trainer_id'])) {
            $trainer = User::find($data['trainer_id']);
            if ($trainer->role !== 'trainer') {
                return response()->json(['message' => 'invalid_trainer_role'], 422);
            }
        }

        $group->update($data);

        return response()->json([
            'message' => 'group_updated',
            'group' => $group
        ]);
    }

    // ===== حذف الجروب =====
    public function destroy(ProgramGroup $group)
    {
        $this->authorize('access', $group);
        $group->delete();

        return response()->json(['message' => 'group_deleted']);
    }

    // ===== إضافة طالب للجروب =====
    public function addStudent(Request $request, ProgramGroup $group)
    {
        // $this->authorize('access-group', $group);

        $data = $request->validate([
            'student_id' => 'required|exists:users,id'
        ]);

        $student = User::find($data['student_id']);
        if ($student->role !== 'student') {
            return response()->json(['message' => 'invalid_student_role'], 422);
        }

       $registered = Registration::where('user_id', $student->id)
    ->where('program_id', $group->program_id)
    ->where('status', 'confirmed') // حسب نظامك
    ->exists();

if (!$registered) {
    return response()->json(['message' => 'student_not_registered'], 403);
}


        GroupStudent::firstOrCreate([
            'group_id' => $group->id,
            'student_id' => $data['student_id']
        ]);

        return response()->json(['message' => 'student_added']);
    }

    // ===== جلب الطلاب داخل جروب معين =====
    public function students(ProgramGroup $group)
    {
        $this->authorize('access', $group);
        return $group->students()->with('studentProfile')->get();
    }

    // ===== جلب جروبات المدرب فقط =====
    public function myGroups(Request $request)
    {
        $trainer = $request->user();
        return ProgramGroup::where('trainer_id', $trainer->id)
            ->with('program')
            ->get();
    }

   public function show($group)
{
    $group = ProgramGroup::with('trainer', 'students')->findOrFail($group);
    return response()->json([
        'data' => $group
    ]);
}


// جلب الطلاب المتاحين للإضافة لجروب معين
public function availableStudents($programId, $groupId)
{
    $students = Registration::with('user')
        ->where('program_id', $programId)
        ->where('status', 'confirmed')
        ->whereDoesntHave('groupStudents', function($q) use ($groupId) {
            $q->where('group_id', $groupId);
        })
        ->get()
        ->map(function ($registration) {
            return [
                'id' => $registration->user->id,
                'name' => $registration->user->name,
            ];
        });

    return response()->json($students);
}





}
