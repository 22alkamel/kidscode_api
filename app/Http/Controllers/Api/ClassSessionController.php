<?php


namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use App\Models\ClassSession;
use App\Models\SessionStudent;
use App\Models\Student_answers as StudentAnswer;

use App\Models\Question;

use Illuminate\Support\Facades\Auth;


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
       ->with(['lesson.questions', 'lesson.media'])
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

    $session = ClassSession::findOrFail($sessionId); // تحتاج معرفة الـ lesson_id
    foreach ($request->answers as $answer) {
    $question = Question::where('lesson_id', $session->lesson_id)
                ->findOrFail($answer['question_id']); // للتأكد من صحة السؤال
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




public function studentGroups()
{
    $student = auth()->user();

    $groups = $student->groups()
        ->with('program:id,title') // اسم البرنامج
        ->select('program_groups.id', 'program_groups.name', 'program_id')
        ->get();

    return response()->json([
        'groups' => $groups
    ]);
}

    // ================= حفظ الإجابات والدرجة =================
    public function submit(Request $request)
    {
        $studentId = Auth::id();
        $sessionId = $request->input('session_id');
        $score = $request->input('score', 0);
        $answers = $request->input('answers', []);

        // حفظ كل إجابة
        foreach ($answers as $ans) {
            $questionId = $ans['question_id'];
            $answerValue = $ans['answer'];

            $question = Question::find($questionId);
            $isCorrect = $question && $question->correct_answer == $answerValue;

            StudentAnswer::updateOrCreate(
                ['student_id' => $studentId, 'question_id' => $questionId],
                ['answer' => $answerValue, 'is_correct' => $isCorrect]
            );
        }

        // حفظ الدرجة النهائية
        $sessionStudent = SessionStudent::updateOrCreate(
            ['session_id' => $sessionId, 'student_id' => $studentId],
            ['score' => $score, 'submitted_at' => now()]
        );

        return response()->json([
            'success' => true,
            'score' => $sessionStudent->score
        ]);
    }

    // ================= جلب الإجابات المحفوظة =================
    public function getSessionStudent($sessionId)
    {
        $studentId = Auth::id();

        $sessionStudent = SessionStudent::where('session_id', $sessionId)
            ->where('student_id', $studentId)
            ->first();

        if (!$sessionStudent) {
            return response()->json([
                'answers' => [],
                'results' => [],
                'score' => null
            ]);
        }

        $studentAnswers = StudentAnswer::where('student_id', $studentId)
            ->whereIn('question_id', function($q) use ($sessionId) {
                $q->select('id')->from('questions')
                  ->where('lesson_id', function($l) use ($sessionId) {
                      $l->select('lesson_id')
                        ->from('class_sessions')
                        ->where('id', $sessionId);
                  });
            })
            ->get();

        $answers = [];
        $results = [];
        foreach ($studentAnswers as $sa) {
            $answers[$sa->question_id] = $sa->answer;
            $results[$sa->question_id] = $sa->is_correct;
        }

        return response()->json([
            'answers' => $answers,
            'results' => $results,
            'score' => $sessionStudent->score
        ]);
    }


}
