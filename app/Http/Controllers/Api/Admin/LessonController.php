<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LessonController extends Controller
{
   public function index($trackId)
{
    return Lesson::with('track')
        ->where('track_id', $trackId)
        ->orderBy('order')
        ->get();
}

public function store(Request $request, $trackId)
{
    $data = $request->validate([
        'title'            => 'required|max:160',
        'content'          => 'nullable',
        'duration_minutes' => 'nullable|integer',
        'is_published'     => 'boolean',
    ]);

    $data['track_id'] = $trackId;
    $data['slug'] = Str::slug($data['title']);

    // ترتيب تلقائي
    $lastOrder = Lesson::where('track_id', $trackId)->max('order');
    $data['order'] = ($lastOrder ?? 0) + 1;

    $lesson = Lesson::create($data);

    return response()->json($lesson, 201);
}

  public function show($id)
    {
        $lesson = Lesson::with(['media', 'questions'])
            ->where('is_published', true)
            ->findOrFail($id);

        return response()->json($lesson);
    }

    // public function show(Lesson $lesson)
    // {
        
    //     return $lesson->load(['media', 'questions']);
    // }

    public function update(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'track_id'          => 'required|exists:tracks,id',
            'title'             => 'required|max:160',
            'content'           => 'nullable',
            'order'             => 'nullable|integer',
            'duration_minutes'  => 'nullable|integer',
            'is_published'      => 'boolean',
        ]);

        $data['slug'] = Str::slug($request->title);

        $lesson->update($data);

        return response()->json($lesson);
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
