<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\SessionStudent;

class SessionController extends Controller
{
    //

    public function store(Request $request)
{
    $session = Session::create([
        'group_id' => $request->group_id,
        'lesson_id' => $request->lesson_id,
        'video_url' => $request->video_url,
        'publish_at' =>now(),
        'is_active' => $request->is_active ?? true,
    ]);

    return response()->json($session);
}


public function studentSessions()
{
    $student = auth()->user();

    $groupIds = $student->groups->pluck('id');

    return Session::whereIn('group_id', $groupIds)
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
    $sessions = Session::with('lesson')
        ->where('group_id', $groupId)
        ->orderBy('publish_at', 'desc')
        ->get();

    return response()->json($sessions);
}



}
