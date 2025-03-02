<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\SubmissionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{

    public function index($lesson_id)
    {
        return response()->json([
            'status' => 'success',
            'data' => Quiz::with('lesson')->where('lesson_id', $lesson_id)->get()
        ]);
    }

    public function store(Request $request, $lesson_id)
    {
        $request->validate(['title' => 'required|string|max:255']);

        try {
            $quiz = Quiz::create(['lesson_id' => $lesson_id, 'title' => $request->title]);
            return response()->json(['status' => 'success', 'data' => $quiz], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Quiz $quiz)
    {
        return response()->json([
            'status' => 'success',
            'data' => $quiz->load('questionsWithAnswers')
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        $request->validate(['title' => 'required|string|max:255']);

        try {
            $quiz->update(['title' => $request->title]);
            return response()->json(['status' => 'success', 'data' => $quiz]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Quiz $quiz)
    {
        try {
            DB::transaction(function () use ($quiz) {
                $quiz->questions()->each(function ($question) {
                    $question->answers()->delete();
                });
                $quiz->questions()->delete();
                $quiz->delete();
            });
            return response()->json(['status' => 'success', 'message' => 'Quiz deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Lấy danh sách câu hỏi của quiz
     */
    public function getQuestions($quiz_id)
    {
        $quiz = Quiz::with('questionsWithAnswers')->find($quiz_id);

        if (!$quiz) {
            return response()->json(['status' => 'error', 'message' => 'Quiz not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $quiz]);
    }

    /**
     * Nộp bài quiz
     */
    public function submitQuiz(Request $request, $quiz_id)
    {
        $user_id = auth()->id();

        $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_id' => 'required|exists:answers,id',
        ]);

        $quiz = Quiz::with('questions.answers')->findOrFail($quiz_id);
        $totalQuestions = $quiz->questions->count();

        if ($totalQuestions == 0) {
            return response()->json(['status' => 'error', 'message' => 'This quiz has no questions'], 400);
        }

        if (count($request->answers) !== $totalQuestions) {
            return response()->json(['status' => 'error', 'message' => 'All questions must be answered'], 400);
        }

        $correctAnswers = 0;

        $submission = DB::transaction(function () use ($request, $quiz, $user_id, &$correctAnswers, $totalQuestions) {
            $submission = Submission::create([
                'quiz_id' => $quiz->id,
                'student_id' => $user_id, // Đổi thành student_id theo model
                'score' => 0,
                'total_questions' => $totalQuestions,
                'correct_answers' => 0,
            ]);

            foreach ($request->answers as $answer) {
                $question = $quiz->questions->firstWhere('id', $answer['question_id']);
                if (!$question) {
                    throw new \Exception("Question {$answer['question_id']} not found in this quiz");
                }

                $correctAnswerIds = json_decode($question->correct_answers, true) ?? [];
                $isCorrect = in_array($answer['answer_id'], $correctAnswerIds);
                $correctAnswers += $isCorrect ? 1 : 0;

                SubmissionAnswer::create([
                    'submission_id' => $submission->id,
                    'question_id' => $answer['question_id'],
                    'answer_ids' => json_encode([$answer['answer_id']]), // Lưu mảng JSON
                    'is_correct' => $isCorrect,
                ]);
            }

            $score = round(($correctAnswers / $totalQuestions) * 10, 2);
            $submission->update([
                'score' => $score,
                'correct_answers' => $correctAnswers,
            ]);

            return $submission;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz submitted successfully',
            'data' => [
                'submission_id' => $submission->id,
                'quiz_id' => $quiz->id,
                'student_id' => $user_id,
                'score' => $submission->score,
                'total_questions' => $submission->total_questions,
                'correct_answers' => $submission->correct_answers,
            ],
        ]);
    }

    /**
     * Cập nhật thứ tự câu hỏi trong quiz
     */
    public function updateQuizOrder(Request $request, Quiz $quiz)
    {
        $request->validate(['orders' => 'required|array']);

        try {
            DB::transaction(function () use ($request, $quiz) {
                foreach ($request->orders as $order => $question_id) {
                    $quiz->questions()->where('id', $question_id)->update(['order' => $order]);
                }
            });
            return response()->json(['status' => 'success', 'message' => 'Question order updated']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Tạo câu hỏi trong quiz
     */
    public function storeQuestion(Request $request, $quiz_id)
    {
        $quiz = Quiz::find($quiz_id);
        if (!$quiz) {
            return response()->json(['status' => 'error', 'message' => 'Quiz not found'], 404);
        }

        $request->validate([
            'question_text' => 'required|string',
            'image_url' => 'nullable|string',
            'is_multiple_choice' => 'required|boolean',
            'correct_answers' => 'required|array',
            'order' => 'required|integer',
        ]);

        try {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $request->question_text,
                'image_url' => $request->image_url,
                'is_multiple_choice' => $request->is_multiple_choice,
                'correct_answers' => json_encode($request->correct_answers),
                'order' => $request->order,
            ]);

            return response()->json(['status' => 'success', 'data' => $question], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Tạo đáp án
     */
    public function storeAnswer(Request $request, $question_id)
    {
        $question = Question::find($question_id);
        if (!$question) {
            return response()->json(['status' => 'error', 'message' => 'Question not found'], 404);
        }

        $order = Answer::where('question_id', $question_id)->count() + 1;

        $request->validate([
            'answer_text' => 'required|string|max:255',
            'is_correct' => 'required|boolean',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $answer = Answer::create([
                'question_id' => $question_id,
                'answer_text' => $request->answer_text,
                'is_correct' => $request->is_correct,
                'note' => $request->note ?? null,
                'order' => $order,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Answer created successfully',
                'data' => $answer,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
