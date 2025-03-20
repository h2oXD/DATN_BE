<?php

namespace App\Imports;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuizImport implements ToCollection
{
    protected $quiz_id;

    public function __construct($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function collection(Collection $rows)
    {
        $rows->shift(); // Bỏ qua tiêu đề nếu cần

        // Lấy giá trị order cao nhất trong bảng questions của quiz hiện tại
        $maxOrder = Question::where('quiz_id', $this->quiz_id)->max('order') ?? 0;

        foreach ($rows as $row) {
            // Kiểm tra câu hỏi đã tồn tại trong quiz chưa
            $existingQuestion = Question::where('quiz_id', $this->quiz_id)
                ->where('question_text', $row[0])
                ->first();

            if ($existingQuestion) {
                continue; // Bỏ qua nếu câu hỏi đã tồn tại
            }

            // Xác định loại câu hỏi
            $questionType = strtolower(trim($row[1])); // Cột "Question Type"
            $isMultipleChoice = $questionType === 'multiple choice' ? 1 : 0;

            // Xử lý danh sách đáp án đúng
            if ($isMultipleChoice) {
                $correctAnswers = explode(',', str_replace(' ', '', $row[7])); // "1,2" → [1,2]
            } else {
                $correctAnswers = [(int) $row[7]]; // "2" → [2]
            }

            // Tạo câu hỏi với order cao nhất hiện tại +1
            $question = Question::create([
                'quiz_id' => $this->quiz_id,
                'question_text' => $row[0],
                'is_multiple_choice' => $isMultipleChoice,
                'image_url' => $row[8] ?? null,
                'order' => ++$maxOrder
            ]);

            // Thêm đáp án
            for ($i = 2; $i <= 6; $i++) {
                if (!empty($row[$i])) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $row[$i],
                        'is_correct' => in_array(($i - 1), $correctAnswers) ? 1 : 0,
                        'order' => $i - 1, // Gán thứ tự cho đáp án
                    ]);
                }
            }
        }
    }
}
