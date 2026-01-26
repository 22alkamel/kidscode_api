<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamProject;
use Illuminate\Http\Request;

class ExamProjectController extends Controller
{
    public function index()
    {
        return ExamProject::with(['program', 'lesson'])
            ->paginate(10);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_id'      => 'required|exists:programs,id',
            'lesson_id'       => 'nullable|exists:lessons,id',
            'title'           => 'required|max:160',
            'description'     => 'nullable',
            'submission_type' => 'required|in:file_upload,text_answer,code_answer,external_link',
            'max_score'       => 'integer',
        ]);

        $exam = ExamProject::create($data);
        return response()->json($exam, 201);
    }

    public function update(Request $request, ExamProject $examProject)
    {
        $data = $request->validate([
            'program_id'      => 'required|exists:programs,id',
            'lesson_id'       => 'nullable|exists:lessons,id',
            'title'           => 'required|max:160',
            'description'     => 'nullable',
            'submission_type' => 'required|in:file_upload,text_answer,code_answer,external_link',
            'max_score'       => 'integer',
        ]);

        $examProject->update($data);

        return response()->json($examProject);
    }

    public function destroy(ExamProject $examProject)
    {
        $examProject->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
