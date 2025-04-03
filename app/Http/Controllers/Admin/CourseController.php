<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatRoom;
use App\Models\ChatRoomUser;
use App\Models\Course;
use App\Models\CourseApprovalHistory;
use App\Models\Lecturer;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\CourseApprove;
use App\Notifications\CourseReject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    const PATH_VIEW = 'admins.courses.';

    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $tag = $request->get('tag');

        $categories = Category::all();

        $courses = Course::with('category', 'tags')->where('status', 'published')
            ->when($search, function ($query, $search) {
                return $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            })
            ->when($category, function ($query, $category) {
                return $query->where('category_id', $category);
            })
            ->when($tag, function ($query, $tag) {
                return $query->whereHas('tags', function ($query) use ($tag) {
                    $query->where('name', 'like', "%$tag%");
                });
            })
            ->latest('id')
            ->paginate(10);
        return view(self::PATH_VIEW . 'index', compact('courses', 'categories'));
    }

    public function show($id)
    {
        $course = Course::with(['category', 'tags', 'user'])->findOrFail($id);
        return view(self::PATH_VIEW . 'show', compact('course'));
    }

    public function approve(Request $request, $id)
    {
        $user = $request->user();
        $course = Course::findOrFail($id);
        if ($course->status != 'pending') {
            return redirect()->route('admin.censor.courses.list')->with('errors', 'Thao tác thất bại');
        }
        $course->status = 'published';
        $course->submited_at = now();
        $course->save();


        $chatRoom = ChatRoom::where('course_id', $course->id)->first();

        if (!$chatRoom) {
            // Tạo phòng chat nếu chưa tồn tại
            $chatRoom = ChatRoom::create([
                'course_id' => $course->id,
                'owner_id' => $course->user_id, // Chủ sở hữu là giảng viên
                'name' => 'Chat nhóm: ' . $course->title,
            ]);

            // Thêm giảng viên vào bảng chat_room_users
            ChatRoomUser::create([
                'chat_room_id' => $chatRoom->id,
                'user_id' => $course->user_id,
                'joined_at' => now(),
            ]);
        }

        CourseApprovalHistory::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $lecturer = User::find($course->user_id);
        $lecturer->notify(new CourseApprove($course, $lecturer));

        return redirect()->route('admin.censor.courses.list')->with('success', 'Khóa học đã được phê duyệt');
    }
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => ' nullable|string|max:500',
        ]);

        $user = $request->user();
        $course = Course::findOrFail($id);
        if ($course->status != 'pending') {
            return redirect()->route('admin.censor.courses.list')->with('errors', 'Thao tác thất bại');
        }
        $course->status = 'draft';
        $course->admin_comment = $request->reason;
        $course->save();

        CourseApprovalHistory::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => 'rejected',
            'comment' => $request->reason,
        ]);

        $lecturer = User::find($course->user_id);
        $lecturer->notify(new CourseReject($course, $lecturer));

        return redirect()->route('admin.censor.courses.list')->with('success', 'Khóa học đã bị từ chối');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $categories = Category::all();
        $tags = Tag::all();
        return view(self::PATH_VIEW . 'edit', compact('course', 'categories', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:pending,published',
            'price' => 'required|numeric',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
            'thumbnail' => 'nullable|image|max:2048',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề khóa học.',
            'title.max' => 'Tiêu đề khóa học không được vượt quá 255 ký tự.',

            'description.required' => 'Vui lòng nhập mô tả cho khóa học.',

            'price.required' => 'Vui lòng nhập giá khóa học.',
            'price.numeric' => 'Giá khóa học phải là một số hợp lệ.',

            'tags.required' => 'Vui lòng chọn thẻ',

            'thumbnail.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'thumbnail.max' => 'Ảnh đại diện không được lớn hơn 2MB.',
        ]);


        try {
            if ($request->hasFile('thumbnail')) {
                if ($course->thumbnail) {
                    Storage::delete($course->thumbnail);
                }
                $data['thumbnail'] = Storage::put('courses', $request->file('thumbnail'));
            }

            $course->update($data);
            if ($request->has('tags')) {
                $course->tags()->sync($request->input('tags'));
            }

            return redirect()->route('admin.courses.index')->with('success', 'Cập nhật khóa học thành công!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function censorCourseList()
    {
        $courses = Course::where('status', 'pending')->get();
        return view('admins.courses.censor-course-list', compact('courses'));
    }
    public function checkCourse($course_id)
    {
        // lấy thông tin khóa học cụ thể theo ID 
        $course = Course::with([
            'user',
            'sections' => function ($query) {
                $query->orderBy('order');
            },
            'sections.lessons' => function ($query) {
                $query->orderBy('order');
            },
            'sections.lessons.documents',
            'sections.lessons.videos',
            'sections.lessons.codings',
            'sections.lessons.quizzes'
        ])->find($course_id);

        if ($course->status != 'pending') {
            return redirect()->route('admin.censor.courses.list');
        }

        $learning_outcomes = json_decode($course->learning_outcomes, true);
        $target_students = json_decode($course->target_students, true);
        $prerequisites = json_decode($course->prerequisites, true);

        $totalLessons = $course->lessons()->count();

        $totalVideoDuration = 0;

        if ($course) {
            foreach ($course->sections as $section) {
                if ($section->lessons) {
                    foreach ($section->lessons as $lesson) {
                        if ($lesson->videos) {
                            $totalVideoDuration += $lesson->videos->duration;
                        }
                    }
                }
            }
        }
        if ($course) {
            foreach ($course->sections as $section) {
                if ($section->lessons) {
                    foreach ($section->lessons as $lesson) {
                        if ($lesson->videos) {
                            $lesson->videos->duration = round($lesson->videos->duration / 60, 1);
                        }
                    }
                }
            }
        }
        $thoiluongvideo = $totalVideoDuration / 60;
        $totalVideoDurationMinutes = round($thoiluongvideo, 1);
        // dd($course->toArray());

        // dd($totalLessons);
        return view(self::PATH_VIEW . 'check-course', compact('totalVideoDurationMinutes', 'course', 'totalLessons', 'learning_outcomes', 'target_students', 'prerequisites'));
    }
    public function approvalHistory()
    {
        $approvalHistories = CourseApprovalHistory::with(['course', 'user'])->latest()->paginate(10);

        return view(self::PATH_VIEW . 'approval_history', compact('approvalHistories'));
    }

    public function showHistory($id)
    {
        $course = Course::with('approvalHistories.user')->findOrFail($id);
        $approvalHistories = $course->approvalHistories;

        return view(self::PATH_VIEW . 'show_history', compact('course', 'approvalHistories'));
    }

}
