<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Support\Str;

class ProgramController extends Controller
{
    public function index()
    {
     
          return Program::orderBy('id', 'desc')->paginate(10);
       
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:160',
        // 'slug' => 'required|string|max:255|unique:programs,slug,',
        'description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'level' => 'required|in:beginner,intermediate,advanced',
        'agemin' => 'nullable|integer',
        'agemax' => 'nullable|integer',
        'duration_weeks' => 'nullable|integer',
        'price'=> 'nullable|integer',
        'is_published' => 'boolean'
    ]);

    // نضيف created_by من المستخدم المسجّل
    $validated['created_by'] = auth()->id();
    
     if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        $validated['image'] = $imagePath;
        }

    // نحتاج توليد slug
    $validated['slug'] = Str::slug($validated['title']).'-'.uniqid();

    $program = Program::create($validated);

    return response()->json($program, 201);
}


    public function show(Program $program)
    {
        return response()->json([
            'program' => $program
        ]);
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->validate([
            'title' => 'string|max:160',
            'slug' => 'nullable|string|max:160|unique:programs,slug,'. $program->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'level' => 'in:beginner,intermediate,advanced',
            'agemin' => 'nullable|integer',
            'agemax' => 'nullable|integer',
            'duration_weeks' => 'nullable|integer',
            'price'=> 'nullable|integer',
            'is_published' => 'boolean',
        ]);

     
// إذا رفع المستخدم صورة جديدة نحذف القديمة
    if ($request->hasFile('image')) {

        if ($program->image && \Storage::disk('public')->exists($program->image)) {
            \Storage::disk('public')->delete($program->image);
        }

        $data['image'] = $request->file('image')->store('images', 'public');
    }

        

        $program->update($data);

        return response()->json([
            'message' => 'program_updated',
            'program' => $program
        ]);
    }

    public function destroy(Program $program)
    {
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
