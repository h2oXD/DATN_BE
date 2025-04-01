<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    // Thêm đánh giá cho khóa học
    public function storeCourseReview(Request $request, $courseId)
    {
        $user = $request->user();

        // Kiểm tra khóa học có tồn tại không
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json(['error' => 'Khóa học không tồn tại'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'reviewable_type' => Course::class,
            'reviewable_id' => $courseId,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
        ]);

        return response()->json(['message' => 'Đánh giá cho khóa học đã được thêm!', 'review' => $review], 201);
    }

    // Thêm đánh giá cho giảng viên
    public function storeLecturerReview(Request $request, $lecturerId)
    {
        $user = $request->user();

        // Kiểm tra giảng viên có tồn tại không
        $lecturer = User::find($lecturerId);
        if (!$lecturer) {
            return response()->json(['error' => 'Giảng viên không tồn tại'], 404);
        }

        // Kiểm tra người dùng có phải là giảng viên không
        $isLecturer = $lecturer->roles()->where('name', 'lecturer')->exists();
        if (!$isLecturer) {
            return response()->json(['error' => 'Người dùng không phải là giảng viên'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'reviewable_type' => User::class,
            'reviewable_id' => $lecturerId,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
        ]);

        return response()->json(['message' => 'Đánh giá cho giảng viên đã được thêm!', 'review' => $review], 201);
    }

    // Cập nhật đánh giá
    public function update(Request $request, $reviewId)
    {
        $user = $request->user();

        // Tìm review, nếu không có thì trả về lỗi 404
        $review = Review::find($reviewId);
        if (!$review) {
            return response()->json(['error' => 'Đánh giá không tồn tại'], 404);
        }

        if ($review->user_id !== $user->id) {
            return response()->json(['error' => 'Bạn không có quyền cập nhật đánh giá này'], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'integer|min:1|max:5',
            'review_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review->update($request->only(['rating', 'review_text']));

        return response()->json(['message' => 'Đánh giá đã được cập nhật!', 'review' => $review]);
    }


    // Xóa đánh giá
    public function destroy(Request $request, $reviewId)
    {
        $user = $request->user();

        // Kiểm tra review có tồn tại không
        $review = Review::find($reviewId);
        if (!$review) {
            return response()->json(['error' => 'Đánh giá không tồn tại'], 404);
        }

        // Kiểm tra quyền xóa
        if ($review->user_id !== $user->id) {
            return response()->json(['error' => 'Bạn không có quyền xóa đánh giá này'], 403);
        }

        $review->delete();
        return response()->json(['message' => 'Đánh giá đã được xóa!']);
    }


    // Lấy danh sách đánh giá của một khóa học
    public function getCourseReviews($courseId)
    {
        $course = Course::find($courseId);
        if (!$course) {
            return response()->json(['error' => 'Khóa học không tồn tại'], 404);
        }

        $user_id = request()->user()->id;
        $isReviewed = Review::where('reviewable_type', Course::class)->where('user_id', $user_id)->where('reviewable_id', $courseId)->first();
        $reviews = Review::where('reviewable_type', Course::class)
            ->with('reviewer')
            ->where('reviewable_id', $courseId)
            ->get();
        $avg_rate = Review::where('reviewable_type', Course::class)
            ->where('reviewable_id', $courseId)
            ->avg('rating');
        return response()->json([
            'reviews' => $reviews,
            'isReviewed' => $isReviewed ? 1 : 0,
            'avgRate' => $avg_rate
        ]);
    }

    // Lấy danh sách đánh giá của một giảng viên
    public function getLecturerReviews($lecturerId)
    {
        $lecturer = User::find($lecturerId);
        if (!$lecturer) {
            return response()->json(['error' => 'Giảng viên không tồn tại'], 404);
        }

        $isLecturer = $lecturer->roles()->where('name', 'lecturer')->exists();
        if (!$isLecturer) {
            return response()->json(['error' => 'Người dùng không phải là giảng viên'], 404);
        }

        $reviews = Review::where('reviewable_type', User::class)
            ->where('reviewable_id', $lecturerId)
            ->get();

        return response()->json($reviews);
    }

    // Lấy danh sách đánh giá của người dùng cho khóa học hoặc giảng viên
    public function getUserReviews(Request $request)
    {
        $user = $request->user();

        $reviews = Review::where('user_id', $user->id)->get();
        return response()->json($reviews);
    }
}
