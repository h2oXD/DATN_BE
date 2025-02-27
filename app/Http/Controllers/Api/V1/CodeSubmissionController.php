<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Coding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CodeSubmissionController extends Controller
{
    protected $judge0Url;
    protected $judge0Headers;

    public function __construct()
    {
        $this->judge0Url = "https://judge0-ce.p.rapidapi.com";
        $this->judge0Headers = [
            'X-RapidAPI-Key' => config('services.judge0.key'), // Load API Key từ config/services.php
            'X-RapidAPI-Host' => 'judge0-ce.p.rapidapi.com',
            'Content-Type' => 'application/json'
        ];
    }

    // Học viên gửi bài làm
    public function submitSolution(Request $request, $coding_id)
    {
        $coding = Coding::findOrFail($coding_id);

        // Kiểm tra dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'language' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $testCases = json_decode($coding->test_cases, true);
        $submissions = [];

        // Kiểm tra ngôn ngữ hợp lệ
        $languageId = $this->getLanguageId($data['language']);
        if (!$languageId) {
            return response()->json(['message' => 'Ngôn ngữ không được hỗ trợ'], 400);
        }

        foreach ($testCases as &$test) {
            if (!is_array($test['input'])) {
                $test['input'] = json_decode($test['input'], true);
            }

            $submissions[] = [
                'source_code' => $data['code'], // Giữ nguyên mã nguồn
                'language_id' => $languageId,
                'stdin' => implode("\n", (array) $test['input']),
                'expected_output' => isset($test['output']) ? trim($test['output']) : "",
            ];
        }
        
        // Gửi bài lên Judge0
        $response = Http::withHeaders($this->judge0Headers)->post("{$this->judge0Url}/submissions/batch?base64_encoded=false&wait=false", [
            'submissions' => $submissions
        ]);
        return response()->json($submissions);
        return response()->json($response->json(), 201);
    }

    // Lấy kết quả bài làm từ Judge0
    public function getSubmissionResult($coding_id, $token)
    {
        if (!$token) {
            return response()->json(['message' => 'Token không hợp lệ'], 400);
        }

        $response = Http::withHeaders($this->judge0Headers)
            ->get("{$this->judge0Url}/submissions/{$token}?base64_encoded=false");

        $result = $response->json();

        if (empty($result) || !isset($result['status'])) {
            return response()->json(['message' => 'Không tìm thấy kết quả. Vui lòng thử lại sau.'], 404);
        }

        return response()->json([
            'message' => 'Kết quả bài làm',
            'status' => $result['status']['description'],
            'stdout' => $result['stdout'] ?? "",
            'expected_output' => $result['expected_output'] ?? "",
            'stderr' => $result['stderr'] ?? "",
            'time' => $result['time'] ?? 0,
            'memory' => $result['memory'] ?? 0
        ]);
    }

    // Map ngôn ngữ với ID của Judge0
    private function getLanguageId($language)
    {
        $languages = [
            'python' => 71,
            'javascript' => 63,
            'java' => 62,
            'cpp' => 54,
            'c' => 50
        ];
        return $languages[strtolower($language)] ?? null;
    }
}
