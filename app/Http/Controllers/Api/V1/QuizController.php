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
    /**
     * Lấy danh sách quiz
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Quiz::with('lesson')->get()
        ]);
    }

    /**
     * Tạo mới quiz
     */
    public function store(Request $request, $lesson_id)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $quiz = Quiz::create(['lesson_id' => $lesson_id, 'title' => $request->title]);

        return response()->json(['status' => 'success', 'data' => $quiz], 201);
    }

    /**
     * Hiển thị thông tin quiz
     */
    public function show(Quiz $quiz)
    {
        return response()->json([
            'status' => 'success',
            'data' => $quiz->load('questions.answers')
        ]);
    }

    /**
     * Cập nhật quiz
     */
    public function update(Request $request, $course_id, $section_id, $lesson_id, $quiz_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $quiz = Quiz::find($quiz_id); // Tìm quiz theo ID

        if (!$quiz) {
            return response()->json(['status' => 'error', 'message' => 'Quiz not found'], 404);
        }

        $quiz->update([
            'title' => $request->title, // Cập nhật title mới
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $quiz // Trả về quiz sau khi cập nhật
        ]);
    }

    /**
     * Xóa quiz
     */
    public function destroy($quiz_id)
    {
        $quiz = Quiz::find($quiz_id);

        if (!$quiz) {
            return response()->json(['status' => 'error', 'message' => 'Quiz not found'], 404);
        }

        DB::transaction(function () use ($quiz) {
            $quiz->questions()->delete();
            $quiz->delete();
        });

        return response()->json(['status' => 'success', 'message' => 'Quiz deleted successfully']);
    }

    /**
     * Lấy danh sách câu hỏi của quiz
     */
    public function getQuestions($quiz_id)
    {
        $quiz = Quiz::with('questions.answers')->find($quiz_id);

        if (!$quiz) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quiz not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $quiz
        ]);
    }

    /**
     * Nộp bài quiz
     */
    public function submitQuiz(Request $request, $quiz_id)
    {
        $user_id = auth()->id(); // Lấy user ID từ authentication

        $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer_id' => 'required|exists:answers,id',
        ]);

        $quiz = Quiz::with('questions')->find($quiz_id);
        if (!$quiz) {
            return response()->json(['status' => 'error', 'message' => 'Quiz not found'], 404);
        }

        $totalQuestions = $quiz->questions->count();
        if ($totalQuestions == 0) {
            return response()->json(['status' => 'error', 'message' => 'This quiz has no questions'], 400);
        }

        $correctAnswers = 0;

        $submission = DB::transaction(function () use ($request, $quiz, $user_id, &$correctAnswers, $totalQuestions) {
            $submission = Submission::create([
                'quiz_id' => $quiz->id,
                'user_id' => $user_id, // Lấy user_id từ auth
                'score' => 0,
                'total_questions' => $totalQuestions,
                'correct_answers' => 0,
            ]);

            foreach ($request->answers as $answer) {
                $question = $quiz->questions->where('id', $answer['question_id'])->first();

                if ($question) {
                    $isCorrect = in_array($answer['answer_id'], json_decode($question->correct_answers, true));
                    $correctAnswers += $isCorrect ? 1 : 0;

                    SubmissionAnswer::create([
                        'submission_id' => $submission->id,
                        'question_id' => $answer['question_id'],
                        'answer_id' => $answer['answer_id'],
                        'is_correct' => $isCorrect,
                    ]);
                }
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
                'user_id' => $user_id,
                'score' => $submission->score,
                'total_questions' => $submission->total_questions,
                'correct_answers' => $submission->correct_answers,
            ],
        ]);
    }


    /**
     * Cập nhật thứ tự câu hỏi trong quiz
     */
    public function updateQuizOrder(Quiz $quiz)
    {
        DB::transaction(function () use ($quiz) {
            $quiz->questions()->update(['order' => 0]);
            $nextQuiz = $quiz->lesson->quizzes()->where('id', '>', $quiz->id)->orderBy('id')->first();
            if ($nextQuiz) {
                $nextQuiz->questions()->update(['order' => 1]);
            }
        });

        return response()->json(['status' => 'success', 'message' => 'Quiz order updated']);
    }

    public function storeQuestion(Request $request, $quiz_id)
    {
        // Kiểm tra xem Quiz có tồn tại không
        $quiz = Quiz::find($quiz_id);
        if (!$quiz) {
            return response()->json(['status' => 'error', 'message' => 'Quiz not found'], 404);
        }

        // Validate dữ liệu đầu vào
        $request->validate([
            'question_text' => 'required|string',
            'image_url' => 'nullable|string',
            'is_multiple_choice' => 'required|boolean',
            'correct_answers' => 'required|array',
            'order' => 'required|integer',
        ]);

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
            // Tạo câu hỏi mới
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $request->question_text,
                'image_url' => $request->image_url,
                'is_multiple_choice' => $request->is_multiple_choice,
                'correct_answers' => json_encode($request->correct_answers), // Lưu dưới dạng JSON
                'order' => $request->order,
            ]);

            DB::commit();

            return response()->json(['status' => 'success', 'data' => $question], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Failed to create question', 'error' => $e->getMessage()], 500);
        }
    }

    public function storeAnswer(Request $request, $question_id)
    {
        // Kiểm tra câu hỏi có tồn tại không
        $question = Question::find($question_id);
        if (!$question) {
            return response()->json([
                'status' => 'error',
                'message' => 'Question not found'
            ], 404);
        }

        // Lấy thứ tự mới dựa trên số lượng câu trả lời hiện có của câu hỏi này
        $order = Answer::where('question_id', $question_id)->count() + 1;

        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'answer_text' => 'required|string|max:255',
            'is_correct' => 'required|boolean', // 1 nếu đúng, 0 nếu sai
            'note' => 'nullable|string|max:500' // Chú thích (có thể null)
        ]);

        // Tạo câu trả lời mới với order tự động
        $answer = Answer::create([
            'question_id' => $question_id,
            'answer_text' => $validated['answer_text'],
            'is_correct' => $validated['is_correct'],
            'note' => $validated['note'] ?? null, // Nếu không có thì để null
            'order' => $order
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Answer created successfully',
            'data' => $answer
        ], 201);
    }
}
