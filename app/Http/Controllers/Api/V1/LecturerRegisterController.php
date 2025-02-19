<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LecturerRegister;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class LecturerRegisterController extends Controller
{
    public function submitAnswers(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('lecturer')) {
            return response()->json(['message' => 'nguoi dung da la giang vien'], Response::HTTP_FORBIDDEN);
        }
        if ((empty($user->bio) || empty($user->profile_picture) || empty($user->phone_number))) {
            return response()->json(['message' => 'ko du thong tin'], Response::HTTP_FORBIDDEN);
        }
        $validator = Validator::make($request->all(), [
            'lecture_answers' => 'required|json',
            'certificate_file' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_FORBIDDEN);
        }

        $data = $validator->validated();

        if (isset($data['certificate_file']) && !empty($data['certificate_file'])) {
            $user->update($data['certificate_file']);
        }
        LecturerRegister::create([
            'user_id' => $user->id,
            'lecture_answers' => $request->lecture_answers
        ]);
        return response()->json(['message' => 'Answers submitted successfully'], Response::HTTP_OK);
    }
}