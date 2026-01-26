<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LessonMedia;
use Illuminate\Http\Request;       // ✅ هذا صحيح
use Illuminate\Support\Facades\Storage;
use App\Models\Lesson;

class LessonMediaController extends Controller
{
    public function store(Request $request, Lesson $lesson)
    {
        $request->validate([
            'type' => 'required|in:video,image,file',
            'file' => 'nullable|file|mimes:mp4,mov,avi,pdf,jpg,jpeg,png',
            'url'  => 'nullable|url',
            'caption' => 'nullable|string',
        ], [
            'file.required_without' => 'يجب رفع ملف أو إدخال رابط',
            'url.required_without'  => 'يجب إدخال رابط أو رفع ملف',
        ]);

        $path = null;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('lesson_media', 'public');
        }

        if ($request->filled('url')) {
            $path = $request->url;
        }

        $media = LessonMedia::create([
            'lesson_id' => $lesson->id,
            'type'      => $request->type,
            'url'       => $path,
            'caption'   => $request->caption,
            'order'     => $lesson->media()->count() + 1,
        ]);

        return response()->json($media, 201);
    }

    public function destroy(LessonMedia $lessonMedia)
    {
        $lessonMedia->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
