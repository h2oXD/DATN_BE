<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OverviewController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/student/home",
     *     summary="Get Top Lecturers",
     *     description="Lấy danh sách giảng viên có điểm đánh giá cao nhất, bao gồm thông tin giảng viên, các khóa học và đánh giá của khóa học.",
     *     tags={"Overview"},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách giảng viên với thông tin khóa học và đánh giá",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="topLectures",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     properties={
     *                         @OA\Property(property="lecturer", type="object",
     *                             properties={
     *                                 @OA\Property(property="id", type="integer", description="ID của giảng viên"),
     *                                 @OA\Property(property="name", type="string", description="Tên giảng viên"),
     *                                 @OA\Property(property="email", type="string", description="Email giảng viên"),
     *                                 @OA\Property(property="email_verified_at", type="string", nullable=true, description="Thời gian xác thực email giảng viên"),
     *                                 @OA\Property(property="phone_number", type="string", description="Số điện thoại giảng viên"),
     *                                 @OA\Property(property="profile_picture", type="string", description="Đường dẫn ảnh đại diện giảng viên"),
     *                                 @OA\Property(property="bio", type="string", nullable=true, description="Giới thiệu giảng viên"),
     *                                 @OA\Property(property="google_id", type="integer", nullable=true, description="ID Google của giảng viên"),
     *                                 @OA\Property(property="linkedin_url", type="string", nullable=true, description="URL LinkedIn của giảng viên"),
     *                                 @OA\Property(property="website_url", type="string", nullable=true, description="URL Website của giảng viên"),
     *                                 @OA\Property(property="created_at", type="string", format="date-time", description="Thời gian tạo giảng viên"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time", description="Thời gian cập nhật giảng viên")
     *                             }
     *                         ),
     *                         @OA\Property(property="courses", type="array", @OA\Items(type="object",
     *                             properties={
     *                                 @OA\Property(property="id", type="integer", description="ID của khóa học"),
     *                                 @OA\Property(property="user_id", type="integer", description="ID của giảng viên sở hữu khóa học"),
     *                                 @OA\Property(property="category_id", type="integer", description="ID danh mục khóa học"),
     *                                 @OA\Property(property="price_regular", type="number", format="float", nullable=true, description="Giá gốc khóa học"),
     *                                 @OA\Property(property="price_sale", type="number", format="float", nullable=true, description="Giá giảm khóa học"),
     *                                 @OA\Property(property="title", type="string", description="Tiêu đề khóa học"),
     *                                 @OA\Property(property="thumbnail", type="string", nullable=true, description="Ảnh thumbnail của khóa học"),
     *                                 @OA\Property(property="video_preview", type="string", nullable=true, description="Video giới thiệu khóa học"),
     *                                 @OA\Property(property="description", type="string", nullable=true, description="Mô tả khóa học"),
     *                                 @OA\Property(property="primary_content", type="string", nullable=true, description="Nội dung chính khóa học"),
     *                                 @OA\Property(property="status", type="string", description="Trạng thái của khóa học"),
     *                                 @OA\Property(property="is_show_home", type="boolean", nullable=true, description="Có hiển thị khóa học trên trang chủ hay không"),
     *                                 @OA\Property(property="target_students", type="string", nullable=true, description="Đối tượng học viên khóa học"),
     *                                 @OA\Property(property="learning_outcomes", type="string", nullable=true, description="Kết quả học tập của khóa học"),
     *                                 @OA\Property(property="prerequisites", type="string", nullable=true, description="Điều kiện tiên quyết của khóa học"),
     *                                 @OA\Property(property="who_is_this_for", type="string", nullable=true, description="Đối tượng khóa học dành cho ai"),
     *                                 @OA\Property(property="is_free", type="boolean", description="Khóa học miễn phí hay không"),
     *                                 @OA\Property(property="language", type="string", nullable=true, description="Ngôn ngữ của khóa học"),
     *                                 @OA\Property(property="level", type="string", nullable=true, description="Trình độ của khóa học"),
     *                                 @OA\Property(property="created_at", type="string", format="date-time", description="Thời gian tạo khóa học"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time", description="Thời gian cập nhật khóa học"),
     *                                 @OA\Property(
     *                                     property="reviews",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         properties={
     *                                             @OA\Property(property="id", type="integer", description="ID của đánh giá"),
     *                                             @OA\Property(property="user_id", type="integer", description="ID của người dùng đánh giá"),
     *                                             @OA\Property(property="course_id", type="integer", description="ID của khóa học đánh giá"),
     *                                             @OA\Property(property="rating", type="integer", description="Điểm đánh giá khóa học"),
     *                                             @OA\Property(property="review_text", type="string", nullable=true, description="Nội dung đánh giá khóa học"),
     *                                             @OA\Property(property="created_at", type="string", nullable=true, format="date-time", description="Thời gian tạo đánh giá")
     *                                         }
     *                                     ),
     *                                    @OA\Property(property="average_rating", type="number", format="float")
     *                                 )
     *                             }
     *                         ))
     *                     }
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="topCourses",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     properties={
     *                         @OA\Property(property="course", type="object",
     *                             properties={
     *                                 @OA\Property(property="id", type="integer", description="ID của khóa học"),
     *                                 @OA\Property(property="user_id", type="integer", description="ID của giảng viên sở hữu khóa học"),
     *                                 @OA\Property(property="category_id", type="integer", description="ID danh mục khóa học"),
     *                                 @OA\Property(property="price_regular", type="number", format="float", nullable=true, description="Giá gốc khóa học"),
     *                                 @OA\Property(property="price_sale", type="number", format="float", nullable=true, description="Giá giảm khóa học"),
     *                                 @OA\Property(property="title", type="string", description="Tiêu đề khóa học"),
     *                                 @OA\Property(property="thumbnail", type="string", nullable=true, description="Ảnh thumbnail của khóa học"),
     *                                 @OA\Property(property="video_preview", type="string", nullable=true, description="Video giới thiệu khóa học"),
     *                                 @OA\Property(property="description", type="string", nullable=true, description="Mô tả khóa học"),
     *                                 @OA\Property(property="primary_content", type="string", nullable=true, description="Nội dung chính khóa học"),
     *                                 @OA\Property(property="status", type="string", description="Trạng thái của khóa học"),
     *                                 @OA\Property(property="is_show_home", type="boolean", nullable=true, description="Có hiển thị khóa học trên trang chủ hay không"),
     *                                 @OA\Property(property="target_students", type="string", nullable=true, description="Đối tượng học viên khóa học"),
     *                                 @OA\Property(property="learning_outcomes", type="string", nullable=true, description="Kết quả học tập của khóa học"),
     *                                 @OA\Property(property="prerequisites", type="string", nullable=true, description="Điều kiện tiên quyết của khóa học"),
     *                                 @OA\Property(property="who_is_this_for", type="string", nullable=true, description="Đối tượng khóa học dành cho ai"),
     *                                 @OA\Property(property="is_free", type="boolean", description="Khóa học miễn phí hay không"),
     *                                 @OA\Property(property="language", type="string", nullable=true, description="Ngôn ngữ của khóa học"),
     *                                 @OA\Property(property="level", type="string", nullable=true, description="Trình độ của khóa học"),
     *                                 @OA\Property(property="created_at", type="string", format="date-time", description="Thời gian tạo khóa học"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time", description="Thời gian cập nhật khóa học"),
     *                                 @OA\Property(
     *                                     property="reviews",
     *                                     type="array",
     *                                     @OA\Items(
     *                                         type="object",
     *                                         properties={
     *                                             @OA\Property(property="id", type="integer", description="ID của đánh giá"),
     *                                             @OA\Property(property="user_id", type="integer", description="ID của người dùng đánh giá"),
     *                                             @OA\Property(property="course_id", type="integer", description="ID của khóa học đánh giá"),
     *                                             @OA\Property(property="rating", type="integer", description="Điểm đánh giá khóa học"),
     *                                             @OA\Property(property="review_text", type="string", nullable=true, description="Nội dung đánh giá khóa học"),
     *                                             @OA\Property(property="created_at", type="string", nullable=true, format="date-time", description="Thời gian tạo đánh giá")
     *                                         }
     *                                     ),
     *                                    @OA\Property(property="average_rating", type="number", format="float")
     *                                 )
     *                             }
     *                         ),
     *                         @OA\Property(property="user", type="object",
     *                             properties={
     *                                 @OA\Property(property="id", type="integer", description="ID của giảng viên"),
     *                                 @OA\Property(property="name", type="string", description="Tên giảng viên"),
     *                                 @OA\Property(property="email", type="string", description="Email giảng viên"),
     *                                 @OA\Property(property="email_verified_at", type="string", nullable=true, description="Thời gian xác thực email giảng viên"),
     *                                 @OA\Property(property="phone_number", type="string", description="Số điện thoại giảng viên"),
     *                                 @OA\Property(property="profile_picture", type="string", description="Đường dẫn ảnh đại diện giảng viên"),
     *                                 @OA\Property(property="bio", type="string", nullable=true, description="Giới thiệu giảng viên"),
     *                                 @OA\Property(property="google_id", type="integer", nullable=true, description="ID Google của giảng viên"),
     *                                 @OA\Property(property="linkedin_url", type="string", nullable=true, description="URL LinkedIn của giảng viên"),
     *                                 @OA\Property(property="website_url", type="string", nullable=true, description="URL Website của giảng viên"),
     *                                 @OA\Property(property="created_at", type="string", format="date-time", description="Thời gian tạo giảng viên"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time", description="Thời gian cập nhật giảng viên")
     *                             }
     *                         ),
     *                         @OA\Property(property="highest_rating", type="number", format="float", description="Điểm đánh giá cao nhất của khóa học"),
     *                     }
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="courseFree",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     properties={
     *                         @OA\Property(property="id", type="integer", description="ID của khóa học"),
     *                         @OA\Property(property="user_id", type="integer", description="ID của giảng viên sở hữu khóa học"),
     *                         @OA\Property(property="category_id", type="integer", description="ID danh mục khóa học"),
     *                         @OA\Property(property="price_regular", type="number", format="float", nullable=true, description="Giá gốc khóa học"),
     *                         @OA\Property(property="price_sale", type="number", format="float", nullable=true, description="Giá giảm khóa học"),
     *                         @OA\Property(property="title", type="string", description="Tiêu đề khóa học"),
     *                         @OA\Property(property="thumbnail", type="string", nullable=true, description="Ảnh thumbnail của khóa học"),
     *                         @OA\Property(property="video_preview", type="string", nullable=true, description="Video giới thiệu khóa học"),
     *                         @OA\Property(property="description", type="string", nullable=true, description="Mô tả khóa học"),
     *                         @OA\Property(property="primary_content", type="string", nullable=true, description="Nội dung chính khóa học"),
     *                         @OA\Property(property="status", type="string", description="Trạng thái của khóa học"),
     *                         @OA\Property(property="is_show_home", type="boolean", nullable=true, description="Có hiển thị khóa học trên trang chủ hay không"),
     *                         @OA\Property(property="target_students", type="string", nullable=true, description="Đối tượng học viên khóa học"),
     *                         @OA\Property(property="learning_outcomes", type="string", nullable=true, description="Kết quả học tập của khóa học"),
     *                         @OA\Property(property="prerequisites", type="string", nullable=true, description="Điều kiện tiên quyết của khóa học"),
     *                         @OA\Property(property="who_is_this_for", type="string", nullable=true, description="Đối tượng khóa học dành cho ai"),
     *                         @OA\Property(property="is_free", type="boolean", description="Khóa học miễn phí hay không"),
     *                         @OA\Property(property="language", type="string", nullable=true, description="Ngôn ngữ của khóa học"),
     *                         @OA\Property(property="level", type="string", nullable=true, description="Trình độ của khóa học"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", description="Thời gian tạo khóa học"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", description="Thời gian cập nhật khóa học"),
     *                         @OA\Property(
     *                             property="reviews",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 properties={
     *                                     @OA\Property(property="id", type="integer", description="ID của đánh giá"),
     *                                     @OA\Property(property="user_id", type="integer", description="ID của người dùng đánh giá"),
     *                                     @OA\Property(property="course_id", type="integer", description="ID của khóa học đánh giá"),
     *                                     @OA\Property(property="rating", type="integer", description="Điểm đánh giá khóa học"),
     *                                     @OA\Property(property="review_text", type="string", nullable=true, description="Nội dung đánh giá khóa học"),
     *                                     @OA\Property(property="created_at", type="string", nullable=true, format="date-time", description="Thời gian tạo đánh giá")
     *                                 }
     *                             ),
     *                            @OA\Property(property="average_rating", type="number", format="float")
     *                         )
     *                     }
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi server",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="message", type="string", description="Thông báo lỗi"),
     *                 @OA\Property(property="error", type="string", description="Chi tiết lỗi")
     *             }
     *         )
     *     )
     * )
     */

    public function overview(Request $request)
    {

        try {
            // $user = request()->user();
            // if($user){
            //     $courses_id = $user->enrollments()->course_id;
            // }

            $lecturers = User::with([
                'courses' => function ($query) {
                    $query->with([
                        'reviews' => function ($q) {
                            $q->where('reviewable_type', Course::class); // Chỉ lấy review của khóa học
                        }
                    ])
                        ->where('status', 'published')
                        ->select([
                            'id',
                            'user_id',
                            'category_id',
                            'price_regular',
                            'price_sale',
                            'title',
                            'thumbnail',
                            'video_preview',
                            'description',
                            'primary_content',
                            'status',
                            'is_show_home',
                            'target_students',
                            'learning_outcomes',
                            'prerequisites',
                            // 'who_is_this_for',
                            'is_free',
                            'language',
                            'level',
                            'created_at',
                            'updated_at'
                        ]);
                }
            ])
                ->whereHas('courses', function ($query) {
                    $query->where('status', 'published')
                        ->whereHas('reviews', function ($q) {
                            $q->where('reviewable_type', Course::class); // Chỉ xét review của khóa học
                        });
                })
                ->get()
                ->map(function ($user) {
                    $publishedCourses = $user->courses->where('status', 'published');

                    $averageRating = $publishedCourses->flatMap(function ($course) {
                        return $course->reviews->pluck('rating');
                    })->avg();

                    return [
                        'lecturer' => $user,
                        'average_rating' => $averageRating ?? 0
                    ];
                })
                ->sortByDesc('average_rating')
                ->take(10)
                ->values();


            $user = $request->user();

            //Course published
            $coursesPublished = Course::with([
                'user',
                'reviews' => function ($query) {
                    $query->where('reviewable_type', Course::class);
                }
            ])
                ->where('status', 'published')
                ->where('is_show_home', 1)
                ->get()
                ->map(function ($course) use ($user) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'thumbnail' => $course->thumbnail,
                        'isLecturer' => $user ? ($course->user_id === $user->id) : false,
                        'isEnrollment' => $user
                            ? Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()
                            : false,
                    ];
                });

            if (count($coursesPublished) == 0) {
                return response()->json([
                    'message' => 'Không có khoá học nào'
                ], 200);
            }





            // top Course
            $courses = Course::with([
                'user',
                'category',
                'reviews' => function ($query) {
                    $query->where('reviewable_type', Course::class);
                }
            ])
                ->where('status', 'published')
                ->get()
                ->map(function ($course) use ($user) {
                    $highestRating = $course->reviews->max('rating');

                    return [
                        'course' => $course,
                        'highest_rating' => number_format($highestRating, 1) ?? 0,
                        'is_lecturer' => $user ? ($course->user_id === $user->id) : false,
                        'is_enrollment' => $user
                            ? Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()
                            : false,
                    ];
                })
                ->sortByDesc('highest_rating')
                ->take(10)
                ->values();

            if (count($courses) == 0) {
                return response()->json([
                    'message' => 'Không có khoá học nào'
                ], 200);
            }

            // Course free
            $coursesFree = Course::with([
                'user',
                'reviews' => function ($query) {
                    $query->where('reviewable_type', Course::class);
                }
            ])
                ->where('is_free', true)
                ->where('status', 'published')
                ->get();



            return response()->json(
                [

                    'topLectures' => $lecturers,
                    'topCourses' => $courses,
                    'courseFree' => $coursesFree,
                    'coursesPublished' => $coursesPublished
                ],
                200
            );
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function courseNew()
    {
        $user = request()->user();

        $courses = Course::with(['user', 'category', 'reviews'])
            ->where('is_show_home', '1')
            ->where('status', 'published')
            ->withAvg('reviews as average_rating', 'rating')
            ->latest('id')
            ->get();

        if (count($courses) == 0) {
            return response()->json([
                'message' => 'Không có khoá học nào'
            ], 200);
        }
        foreach ($courses as $course) {
            $isLecturer = $user ? ($course->user_id === $user->id) : false;
            $isEnrollment = $user
                ? Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()
                : false;
            $newCourse[] = [
                'id' => $course->id,
                'user_id' => $course->user_id,
                'category_name' => $course->category->name,
                'lecturer_name' => $course->user->name,
                'title' => $course->title,
                'thumbnail' => $course->thumbnail,
                'price_regular' => $course->price_regular,
                'price_sale' => $course->price_sale,
                'is_free' => $course->is_free,
                'status' => $course->status,
                'average_rating' => number_format($course->average_rating, 1) ?? 0,
                'level' => $course->level,
                'is_lecturer' => $isLecturer,
                'is_enrollment' => $isEnrollment
            ];
        }
        return response()->json([
            'data' => $newCourse
        ], 200);
    }
    public function guestLecturer()
    {
        $lecturers = User::whereHas('roles', function ($query) {
            $query->where('name', 'lecturer');
        })
            ->with([
                'reviews' => function ($query) {
                    $query->orderByDesc('rating'); // Sắp xếp đánh giá theo rating giảm dần
                }
            ])
            ->withCount(['reviews', 'courses']) // Đếm số lượng đánh giá
            ->withAvg('reviews', 'rating') // Lấy trung bình rating
            ->orderByDesc('reviews_count') // Sắp xếp theo số đánh giá nhiều nhất
            ->get();
        return response()->json($lecturers);
    }
    public function guestLecturerInfo($lecturer_id)
    {
        $lecturers = User::with(['courses', 'reviews'])->find($lecturer_id);
        return response()->json($lecturers);
    }

    public function courseCategory(Request $request)
    {

        $user = $request->user();
        $categoryId = $request->query('category_id');

        if ($categoryId && !Category::where('id', $categoryId)->exists()) {
            return response()->json([
                'message' => 'Danh mục không tồn tại.'
            ], 404);
        }

        $query = Course::with(['user:id,name,email', 'category:id,name'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('status', 'published');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $courses = $query->get();

        foreach ($courses as $course) {
            $course->setAttribute('is_lecturer', $user && $course->user_id === $user->id);
            $course->setAttribute('is_enrolled', $user
                ? Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()
                : false);
        }

        return response()->json([
            'message' => $categoryId ? 'Lọc theo danh mục thành công.' : 'Hiển thị tất cả khóa học.',
            'data' => $courses
        ]);
    }


    public function courseCategoryGuest(Request $request)
    {

        $user = $request->user();
        $categoryId = $request->query('category_id');

        if ($categoryId && !Category::where('id', $categoryId)->exists()) {
            return response()->json([
                'message' => 'Danh mục không tồn tại.'
            ], 404);
        }

        $query = Course::with(['user:id,name,email', 'category:id,name'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('status', 'published');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $courses = $query->get();

        foreach ($courses as $course) {
            $course->setAttribute('is_lecturer', $user && $course->user_id === $user->id);
            $course->setAttribute('is_enrolled', $user
                ? Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()
                : false);
        }

        return response()->json([
            'message' => $categoryId ? 'Lọc theo danh mục thành công.' : 'Hiển thị tất cả khóa học.',
            'data' => $courses
        ]);
    }
}
