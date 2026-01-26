<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\Program;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    // إحضار كل التراكات داخل برنامج
    public function index(Program $program)
    {
        return $program->tracks()->with('lessons')->orderBy('order')->get();
    }

    // إنشاء تراك جديد
    public function store(Request $request, Program $program)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'order' => 'integer|min:1'
        ]);

        $track = Track::create([
            'program_id' => $program->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'order' => $data['order'] ?? 1
        ]);

        return response()->json([
            'message' => 'track_created',
            'track' => $track
        ], 201);
    }

    // تحديث تراك
    public function update(Request $request, Track $track)
    {
        $data = $request->validate([
            'title' => 'string|max:200',
            'description' => 'nullable|string',
            'order' => 'integer|min:1'
        ]);

        $track->update($data);

        return response()->json([
            'message' => 'track_updated',
            'track' => $track
        ]);
    }

    // حذف تراك
    public function destroy(Track $track)
    {
        $track->delete();

        return response()->json(['message' => 'track_deleted']);
    }
}
