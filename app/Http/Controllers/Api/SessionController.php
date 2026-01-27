<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use App\Models\ClassSession;
use App\Models\SessionStudent;
use App\Models\Question;
use App\Models\StudentAnswer;


class ClassSessionController extends Controller
{
    //

    public function store(Request $request)
{
    $data = $request->validate([
        'group_id' => 'required|exists:program_groups,id',
        'lesson_id' => 'required|exists:lessons,id',
        'video_url' => 'nullable|string',
        'is_active' => 'boolean',
    ]);

    $data['publish_at'] = now();

    $classSession = ClassSession::create($data);

    return response()->json($classSession, 201);
}



public function studentSessions()
{
    $student = auth()->user();

    $groupIds = $student->groups->pluck('id');

    return ClassSession::whereIn('group_id', $groupIds)
        ->where('publish_at', '<=', now())
        ->with('lesson')
        ->orderBy('publish_at', 'desc')
        ->get();
}


public function markWatched($sessionId)
{
    $student = auth()->user();

    SessionStudent::updateOrCreate(
        [
            'session_id' => $sessionId,
            'student_id' => $student->id
        ],
        [
            'watched_at' => now()
        ]
    );

    return response()->json(['status' => 'watched']);
}


public function submitAnswers(Request $request, $sessionId)
{
    $student = auth()->user();
    $score = 0;

    foreach ($request->answers as $answer) {
        $question = Question::find($answer['question_id']);
        $isCorrect = $question->correct_answer == $answer['answer'];

        StudentAnswer::create([
            'student_id' => $student->id,
            'question_id' => $question->id,
            'answer' => $answer['answer'],
            'is_correct' => $isCorrect
        ]);

        if ($isCorrect) $score++;
    }

    SessionStudent::updateOrCreate(
        [
            'session_id' => $sessionId,
            'student_id' => $student->id
        ],
        [
            'submitted_at' => now(),
            'score' => $score
        ]
    );

    return response()->json(['score' => $score]);
}

public function sessionReport($sessionId)
{
    return SessionStudent::with('student')
        ->where('session_id', $sessionId)
        ->get();
}

public function groupSessions($groupId)
{
    $classSession = ClassSession::with('lesson')
        ->where('group_id', $groupId)
        ->orderBy('publish_at', 'desc')
        ->get();

    return response()->json($classSession);
}



}
