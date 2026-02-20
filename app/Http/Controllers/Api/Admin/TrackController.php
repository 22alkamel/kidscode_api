<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Program;

class TrackController extends Controller
{
   public function index($program)
{
    $program = Program::where('id', $program)
        ->orWhere('slug', $program)
        ->firstOrFail();

    return Track::where('program_id', $program->id)
        ->orderBy('order')
        ->paginate(10);
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'program_id'        => 'required|exists:programs,id',
            'title'             => 'required|max:160',
            'track_img'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description'       => 'nullable',
            'order'             => 'nullable|integer',
            'estimated_time'    => 'nullable|integer',
            'is_published'      => 'boolean',
        ]);

         if ($request->hasFile('track_img')){
        $imagePath = $request->file('track_img')->store('track_img', 'public');
        $data['track_img'] = $imagePath;
        }

        $data['slug'] = Str::slug($request->title);

        $track = Track::create($data);
        return response()->json($track, 201);
    }

    public function show(Track $track)
    {
        return $track->load('lessons');
    }

    public function update(Request $request, Track $track)
    {
        $data = $request->validate([
            'program_id'        => 'required|exists:programs,id',
            'title'             => 'string|max:160',
            'track_img'=> 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description'       => 'nullable',
            'order'             => 'nullable|integer',
            'estimated_time'    => 'nullable|integer',
            'is_published'      => 'boolean',
        ]);


         if ($request->hasFile('track_img')) {

        if ($track->track_img && \Storage::disk('public')->exists($track->track_img)) {
            \Storage::disk('public')->delete($track ->track_img );
        }

        $data['track_img'] = $request->file('track_img')->store('track_img', 'public');
    }


        $data['slug'] = Str::slug($request->title);

        $track->update($data);
        return response()->json($track);
    }

    public function destroy(Track $track)
    {
        $track->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function tracksForProgram(\App\Models\Program $program)
{
    return [
        'tracks' => Track::where('program_id', $program->id)->orderBy('order')->get()
    ];
}

}
