<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\ExamProject;
use Illuminate\Http\Request;


class ExamProjectController extends Controller
{
    public function index($programId)
    {
        return response()->json(
            ExamProject::where('program_id', $programId)->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'title' => 'required|string|max:160',
            'description' => 'nullable|string',
            'submission_type' => 'required|in:file_upload,text_answer,code_answer,external_link',
            'max_score' => 'nullable|integer',
        ]);

        return response()->json(
            ExamProject::create($data),
            201
        );
    }
}
