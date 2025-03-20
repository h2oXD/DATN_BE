<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Imports\QuizImport;
use App\Models\Answer;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\SubmissionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Section;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    /**
     * @OA\Get(
     *     path="/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes",
     *     summary="Lấy danh sách tất cả bài kiểm tra",
     *     description="API này trả về danh sách tất cả các bài kiểm tra có kèm thông tin bài học liên quan.",
     *     tags={"Quizzes"},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách tất cả bài kiểm tra",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="lesson", type="object",
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="title", type="string", example="Lập trình PHP cơ bản")
     *                     ),
     *                     @OA\Property(property="title", type="string", example="Bài kiểm tra Laravel"),
     *                     @OA\Property(property="description", type="string", example="Kiểm tra kiến thức về Laravel cơ bản"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-24T12:34:56Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-24T13:45:10Z")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Quiz::with('lesson')->get()
        ]);
    }

    /**
     * @OA\Post(
     *     path="/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes",
     *     summary="Tạo một bài kiểm tra mới",
     *     description="API này tạo một bài kiểm tra mới cho một bài học cụ thể.",
     *     tags={"Quizzes"},
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài học",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="Bài kiểm tra PHP")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bài kiểm tra được tạo thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="lesson_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Bài kiểm tra PHP"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-24T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-24T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The title field is required."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="title", type="array",
     *                     @OA\Items(type="string", example="The title field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request, $lesson_id)
    {
        $request->validate(['title' => 'required|string|max:255']);

        $quiz = Quiz::create(['lesson_id' => $lesson_id, 'title' => $request->title]);

        return response()->json(['status' => 'success', 'data' => $quiz], 201);
    }

    /**
     * @OA\Get(
     *     path="/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes/{quiz_id}",
     *     summary="Lấy thông tin chi tiết của một bài kiểm tra",
     *     description="API này trả về thông tin chi tiết của một bài kiểm tra, bao gồm danh sách câu hỏi và các đáp án.",
     *     tags={"Quizzes"},
     *     @OA\Parameter(
     *         name="quiz_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài kiểm tra",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin bài kiểm tra",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Bài kiểm tra Laravel"),
     *                 @OA\Property(property="questions", type="array",
     *                     @OA\Items(type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="quiz_id", type="integer", example=1),
     *                         @OA\Property(property="question_text", type="string", example="Laravel là gì?"),
     *                         @OA\Property(property="answers", type="array",
     *                             @OA\Items(type="object",
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="question_id", type="integer", example=1),
     *                                 @OA\Property(property="answer_text", type="string", example="Laravel là một framework PHP"),
     *                                 @OA\Property(property="is_correct", type="boolean", example=true)
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-24T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-24T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy bài kiểm tra",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy bài kiểm tra")
     *         )
     *     )
     * )
     */
    public function show(Quiz $quiz)
    {
        return response()->json([
            'status' => 'success',
            'data' => $quiz->load('questions.answers')
        ]);
    }

    /**
     * @OA\Put(
     *     path="/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes/{quiz_id}",
     *     summary="Cập nhật thông tin của một bài kiểm tra",
     *     description="API này cho phép cập nhật tiêu đề của một bài kiểm tra.",
     *     tags={"Quizzes"},
     *     @OA\Parameter(
     *         name="course_id",
     *         in="path",
     *         required=true,
     *         description="ID của khóa học",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="section_id",
     *         in="path",
     *         required=true,
     *         description="ID của phần học",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="lesson_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài học",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="quiz_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài kiểm tra",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="Bài kiểm tra nâng cao về Laravel")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bài kiểm tra được cập nhật thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="lesson_id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Bài kiểm tra nâng cao về Laravel"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-24T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-24T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy bài kiểm tra",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Quiz not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Lỗi xác thực dữ liệu đầu vào",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The title field is required."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="title", type="array",
     *                     @OA\Items(type="string", example="The title field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/lecturer/courses/{course_id}/sections/{section_id}/lessons/{lesson_id}/quizzes/{quiz_id}",
     *     summary="Xóa bài kiểm tra",
     *     description="API này cho phép xóa một bài kiểm tra cùng với tất cả các câu hỏi liên quan.",
     *     tags={"Quizzes"},
     *     @OA\Parameter(
     *         name="quiz_id",
     *         in="path",
     *         required=true,
     *         description="ID của bài kiểm tra cần xóa",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bài kiểm tra đã được xóa thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Quiz deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy bài kiểm tra",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Quiz not found")
     *         )
     *     )
     * )
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
        $quiz = Quiz::with('questions.answers')->where('lesson_id', $quiz_id)->first();

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

    // tạo câu hỏi trong question
    public function storeQuestion(Request $request, $quiz_id)
    {
        // Kiểm tra xem Quiz có tồn tại không
        $quiz = Quiz::where('lesson_id', $quiz_id)->first();
        if (!$quiz) {
            return response()->json(['status' => 'error', 'message' => 'Quiz not found'], 404);
        }

        // Validate dữ liệu đầu vào
        $request->validate([
            'question_text' => 'required|string',
            'is_multiple_choice' => 'required|in:0,1',
            'answers' => 'required|array',
        ]);

        $questionData['quiz_id'] = $quiz->id;
        $questionData['question_text'] = $request->question_text;
        $questionData['is_multiple_choice'] = $request->is_multiple_choice;
        $answersData = $request->answers;
        // Lọc bỏ các answer rỗng
        $answersData = array_filter($answersData, function ($answer) {
            return isset($answer['text']) && trim($answer['text']) !== '';
        });


        // Lấy order lớn nhất hiện có cho quiz này
        $maxOrder = Question::where('quiz_id', $quiz->id)->max('order');
        $newOrder = $maxOrder ? $maxOrder + 1 : 1;
        $questionData['order'] = $newOrder;
        DB::beginTransaction();
        try {
            if ($request->is_multiple_choice == 1) {
                $question = Question::create($questionData);
                foreach ($answersData as $index => $answers) {
                    if (!isset($answers['is_correct'])) {
                        Answer::create([
                            'answer_text' => $answers['text'],
                            'is_correct' => 0,
                            'question_id' => $question->id,
                            'order' => $index + 1
                        ]);
                    } else {
                        Answer::create([
                            'answer_text' => $answers['text'],
                            'is_correct' => $answers['is_correct'][0],
                            'question_id' => $question->id,
                            'order' => $index + 1
                        ]);
                    }
                }
            }
            if ($questionData['is_multiple_choice'] == 0) {
                $correctIndex = $request->is_correct; // Lấy index của câu trả lời đúng
                $answersData[$correctIndex]['is_correct'] = 1;
                $question = Question::create($questionData);
                foreach ($answersData as $index => $answers) {
                    if (!isset($answers['is_correct'])) {
                        Answer::create([
                            'answer_text' => $answers['text'],
                            'is_correct' => 0,
                            'question_id' => $question->id,
                            'order' => $index + 1
                        ]);
                    } else {
                        Answer::create([
                            'answer_text' => $answers['text'],
                            'is_correct' => $answers['is_correct'],
                            'question_id' => $question->id,
                            'order' => $index + 1
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Thêm câu hỏi thành công',
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Lỗi hệ thống ' . $th,
            ], 500);
        }
    }


    // tạo đáp án
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



    public function uploadQuizExcel(Request $request, $lessonId, $quiz_id)
    {

        // Kiểm tra xem lesson có tồn tại không
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            return response()->json(['message' => 'Lesson không tồn tại'], 404);
        }

        // Kiểm tra xem quiz có thuộc lesson này không (nếu có quan hệ)
        $quiz = Quiz::where('id', $quiz_id)->where('lesson_id', $lessonId)->first();
        if (!$quiz) {
            return response()->json(['message' => 'Quiz không thuộc lesson này'], 404);
        }

        // Validate file upload
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Vui lòng chọn một file để import.',
            'file.mimes'    => 'File phải có định dạng: xlsx, xls, hoặc csv.',
            'file.max'      => 'File không được lớn hơn 2MB.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Lưu file vào storage
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('excels', $fileName, 'public');

        // Import dữ liệu từ file đã lưu
        Excel::import(new QuizImport($quiz_id), Storage::path($filePath));
        Storage::delete($filePath);

        return response()->json([
            'message' => 'Import thành công!',
            
        ]);
    }
}
