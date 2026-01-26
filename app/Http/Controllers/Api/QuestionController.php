<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Question;

use Illuminate\Http\Request;


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

    return $lesson->questions()->create($data);
}


    public function destroy($id)
    {
        Question::findOrFail($id)->delete();
        return response()->json(['message' => 'Question deleted']);
    }
}
