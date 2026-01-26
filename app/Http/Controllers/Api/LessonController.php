<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Track;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    // جلب دروس مسار معين
    public function index($trackId)
    {
        $lessons = Lesson::where('track_id', $trackId)
            ->where('is_published', true)
            ->with(['media', 'questions'])
            ->orderBy('order')
            ->get();

        return response()->json($lessons);
    }

    // عرض درس واحد بكل محتواه

     public function show(Lesson $lesson)
    {
        $lesson->load(['media', 'questions'])->where('is_published', true);
        return response()->json($lesson);
    }

    // public function show($id)
    // {
    //     $lesson = Lesson::with(['media', 'questions'])
    //         ->where('is_published', true)
    //         ->findOrFail($id);

    //     return response()->json($lesson);
    // }

    // إنشاء درس
    public function store(Request $request)
    {
        $data = $request->validate([
            'track_id' => 'required|exists:tracks,id',
            'title' => 'required|string|max:160',
            'slug' => 'required|string|unique:lessons,slug',
            'content' => 'nullable|string',
            'order' => 'nullable|integer',
            'duration_minutes' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        $lesson = Lesson::create($data);

        return response()->json($lesson, 201);
    }

    // تحديث درس
   public function update(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'duration_minutes' => 'nullable|integer',
            'is_published' => 'boolean',
        ]);

        $lesson->update($data);

        return response()->json($lesson);
    }

    // حذف درس
    public function destroy($id)
    {
        Lesson::findOrFail($id)->delete();
        return response()->json(['message' => 'Lesson deleted']);
    }
}

