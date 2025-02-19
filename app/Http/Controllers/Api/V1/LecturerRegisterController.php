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
            'answer1' => 'required|max:255',
            'answer2' => 'required|max:255',
            'answer3' => 'required|max:255',
            'certificate_file' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_FORBIDDEN);
        }

        // $data = $validator->validated();
        // $decodedAnswers = json_decode(json_decode($request->lecture_answers,true) ,true);
        // if (!$decodedAnswers) {
        //     return response()->json(['message' => 'Dữ liệu không hợp lệ'], Response::HTTP_BAD_REQUEST);
        // }

        // return response()->json($decodedAnswers);

        if ($request->hasFile('certificate_file')) {
            $path = $request->file('certificate_file')->store('certificates');
            $user->update(['certificate_file' => $path]);
        }
        LecturerRegister::create([
            'user_id' => $user->id,
            'answer1' => $request->answer1,
            'answer2' => $request->answer2,
            'answer3' => $request->answer3,

        ]);
        return response()->json(['message' => 'Answers submitted successfully'], Response::HTTP_OK);
    }
}
