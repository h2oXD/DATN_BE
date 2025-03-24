<?php

namespace App\Imports;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuizImport implements ToCollection
{
    protected $quiz_id;
    protected $errors = [];

    public function __construct($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function collection(Collection $rows)
    {
        // Kiểm tra số lượng cột hợp lệ
        $expectedColumns = [
            'Question Text', 'Question Type', 'Option 1', 'Option 2', 'Option 3', 'Option 4', 'Option 5', 'Correct Answer', 'Image URL'
        ];

        if ($rows->count() < 2) {
            $this->errors[] = "File Excel không có dữ liệu!";
            return;
        }

        $header = array_values(array_filter(array_map(fn($col) => preg_replace('/\s+/', ' ', trim($col)), $rows->first()->toArray())));



      
        if ($header !== $expectedColumns) {
            $this->errors[] = "Cấu trúc file không hợp lệ. Hãy đảm bảo đúng tiêu đề các cột.";
            return;
        }

        $rows->shift(); // Bỏ qua tiêu đề

        $maxOrder = Question::where('quiz_id', $this->quiz_id)->max('order') ?? 0;

        foreach ($rows as $index => $row) {
            if ($row->filter()->isEmpty()) continue; // Bỏ qua dòng trống

            // Kiểm tra dữ liệu
            $validator = Validator::make($row->toArray(), [
                0 => 'required|string',  // Question Text
                1 => 'required|in:One Choice,Multiple Choice', // Question Type
                7 => 'required|string', // Correct Answer
            ], [
                '0.required' => "Dòng " . ($index + 2) . ": Câu hỏi không được để trống.",
                '1.required' => "Dòng " . ($index + 2) . ": Loại câu hỏi không hợp lệ.",
                '1.in' => "Dòng " . ($index + 2) . ": Question Type chỉ được là 'One Choice' hoặc 'Multiple Choice'.",
                '7.required' => "Dòng " . ($index + 2) . ": Phải có đáp án đúng."
            ]);

            if ($validator->fails()) {
                $this->errors = array_merge($this->errors, $validator->errors()->all());
                continue;
            }

            $questionText = $row[0];
            $questionType = $row[1];
            $correctAnswers = explode(',', str_replace(' ', '', $row[7])); // Xóa khoảng trắng
            $isMultipleChoice = $questionType === 'Multiple Choice' ? 1 : 0;

            $existingQuestion = Question::where('quiz_id', $this->quiz_id)
                ->where('question_text', $questionText)
                ->first();

            if ($existingQuestion) {
                continue; // Nếu câu hỏi đã tồn tại, bỏ qua
            }

            $question = Question::create([
                'quiz_id' => $this->quiz_id,
                'question_text' => $questionText,
                'is_multiple_choice' => $isMultipleChoice,
                'image_url' => $row[8] ?? null,
                'order' => ++$maxOrder
            ]);

            for ($i = 2; $i <= 6; $i++) {
                if (!empty($row[$i])) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $row[$i],
                        'is_correct' => in_array(($i - 1), $correctAnswers) ? 1 : 0,
                        'order' => $i - 1,
                    ]);
                }
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}