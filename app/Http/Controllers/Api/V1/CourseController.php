<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lecturer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function lecturerCreateCourse(Request $request)
    {
        $userID = $request->user()->id;
        $lecturer = Lecturer::where('user_id',$userID)->first();
        $course = Course::create([
            'title' => $request->title,
            'lecturer_id' => $lecturer->id,
            'category_id' => $request->category_id,
            'status' => 'draft',
            'admin_commission_rate' => 30,
            'price' => 1,
            'price_sale' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => null
        ]);

        return response()->json([
            'data' => $course,
        ]);
    }
}
