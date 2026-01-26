<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;   // ✅ هذا هو السطر الصحيح
use App\Models\Lesson;
use App\Models\LessonMedia;



class LessonMediaController extends Controller
{
    public function store(Request $request, Lesson $lesson)
{
    $data = $request->validate([
        'type'    => 'required|in:video,image,file',
        'url'     => 'required',
        'caption' => 'nullable',
    ]);

    $data['lesson_id'] = $lesson->id;
    $data['order'] = ($lesson->media()->max('order') ?? 0) + 1;

    return $lesson->media()->create($data);
}


    public function destroy($id)
    {
        LessonMedia::findOrFail($id)->delete();
        return response()->json(['message' => 'Media deleted']);
    }
}
