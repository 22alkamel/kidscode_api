<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\Lesson;


class QuestionController extends Controller
{
   public function store(Request $request, Lesson $lesson)
{
    $data = $request->validate([
        'type' => 'required|in:multiple_choice,true_false,fill_blank,code_output',
        'question' => 'required',
        'options' => 'nullable|array',
        'correct_answer' => 'nullable',
    ]);

    $data['lesson_id'] = $lesson->id;
    $data['order'] = ($lesson->questions()->max('order') ?? 0) + 1;

    return response()->json($lesson->questions()->create($data));
}


  
public function update(Request $request, $lessonId, $questionId)
{
    $question = Question::where('id', $questionId)
        ->where('lesson_id', $lessonId)
        ->firstOrFail();

    $data = $request->validate([
        'type' => 'required|in:multiple_choice,true_false,fill_blank,code_output',
        'question' => 'required',
        'options' => 'nullable|array',
        'correct_answer' => 'nullable',
    ]);

    $question->update($data);

    return response()->json($question);
}



    public function destroy(Question $question)
    {
        $question->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
