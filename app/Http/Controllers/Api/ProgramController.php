<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
//     public function index()
//     {
//         return Program::where('is_published', true)->paginate(10);
//     }

//    public function show($slug)
//    {
//       return Program::where('slug', $slug)->firstOrFail();
//    }


public function index(Request $req) {
  if ($req->has('age')) {
    $age = (int) $req->age;
    // أبحث عن برنامج يغطي هذا العمر
    $program = Program::where('agemin','<=',$age)
                      ->where('agemax','>=',$age)
                      ->with('tracks')
                      ->first();
    if (!$program) return response()->json(['data' => null], 200);
    return response()->json(['data' => $program], 200);
  }
  // fallback: كل البرامج (أو صفح)
  $programs = Program::with('tracks')->get();
  return response()->json(['data' => $programs], 200);
}

public function show($id) {
  $program = Program::with('tracks')->findOrFail($id);
  return response()->json(['data' => $program], 200);
}

public function tracks($id) {
  $program = Program::findOrFail($id);
  return response()->json(['data' => $program->tracks], 200);
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:160',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'agemin' => 'nullable|integer',
            'agemax' => 'nullable|integer',
            'duration_weeks' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        $this->authorize('manage_programs');
        $program = Program::create([
            ...$data,
            'slug' => Str::slug($data['title']) . '-' . uniqid(),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'program_created',
            'program' => $program
        ], 201);
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'title' => 'string|max:160',
            'description' => 'nullable|string',
            'level' => 'in:beginner,intermediate,advanced',
            'agemin' => 'nullable|integer',
            'agemax' => 'nullable|integer',
            'duration_weeks' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        $this->authorize('manage_programs');

        $program->update($data);

        return response()->json([
            'message' => 'program_updated',
            'program' => $program
        ]);
    }

    public function destroy(Program $program)
    {
        $this->authorize('manage_programs');

        $program->delete();

        return response()->json([
            'message' => 'program_deleted'
        ]);
    }

    public function publish(Program $program)
{
    $program->update(['is_published' => true]);

    return response()->json([
        'message' => 'program_published',
        'program' => $program
    ]);
}



}
