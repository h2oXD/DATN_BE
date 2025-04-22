<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountStatusChanged;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Progress;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\TransactionWallet;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    const PATH_VIEW = 'admins.users.';

    public function indexLecturers(Request $request)
    {
        $search = $request->get('search');

        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'lecturer');
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            })
            ->latest('id')
            ->paginate(5);

        return view(self::PATH_VIEW . 'lecturer', compact('users'));
    }

    public function indexStudents(Request $request)
    {
        $search = $request->get('search');

        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'student');
        })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(5)
            ->withQueryString(); // Giữ lại query search khi chuyển trang

        return view(self::PATH_VIEW . 'student', compact('users'));
    }


    public function create()
    {
        $roles = Role::all();
        return view(self::PATH_VIEW . 'create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|max:20',
            'profile_picture' => 'nullable|image|max:2048',
            'role' => 'required|in:lecturer,student',
            'password' => 'required|min:8|confirmed',
            'bio' => 'nullable|string',
            'google_id' => 'nullable|string',
            // 'status' => 'nullable|in:0,1,2',
            'country' => 'nullable|string',
            'province' => 'nullable|string',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'gender' => 'nullable|in:male,female,other',
            'linkedin_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'certificate_file' => 'nullable|file',
            'bank_name' => 'nullable|string',
            'bank_nameUser' => 'nullable|string',
            'bank_number' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'birth_date.before_or_equal' => 'Ngày sinh không được là ngày trong tương lai.',

            'phone_number.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'profile_picture.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'profile_picture.max' => 'Ảnh đại diện không được lớn hơn 2MB.',

            'role.required' => 'Vui lòng chọn vai trò.',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        try {
            if ($request->hasFile('profile_picture')) {
                $data['profile_picture'] = Storage::put('profile_pictures', $request->file('profile_picture'));
            }

            if ($request->hasFile('certificate_file')) {
                $data['certificate_file'] = Storage::put('certificates', $request->file('certificate_file'));
            }

            // Mã hóa mật khẩu
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            // Tạo ví cho người dùng
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0
            ]);

            // Gán vai trò cho người dùng
            $role = Role::where('name', $data['role'])->first();
            $user->roles()->attach($role);

            // Nếu người dùng là giảng viên, thêm vai trò học viên
            if ($data['role'] === 'lecturer') {
                $studentRole = Role::where('name', 'student')->first();
                $user->roles()->attach($studentRole);
            }

            return redirect()->route($data['role'] === 'lecturer' ? 'admin.lecturers.index' : 'admin.students.index')
                ->with('success', 'Tạo user thành công!');
        } catch (\Throwable $th) {
            // Xóa ảnh nếu có lỗi xảy ra
            if (!empty($data['profile_picture']) && Storage::exists($data['profile_picture'])) {
                Storage::delete($data['profile_picture']);
            }
            if (!empty($data['certificate_file']) && Storage::exists($data['certificate_file'])) {
                Storage::delete($data['certificate_file']);
            }
            return back()->with('success', false)->with('error', $th->getMessage());
        }
    }

    public function show(User $user)
    {
        return view(self::PATH_VIEW . 'show', compact('user'));
    }
    public function showLecturer($id)
    {
        $user = User::with('roles')->findOrFail($id);

        // Lấy tất cả các khóa học đã publish của giảng viên
        $courses = Course::where('user_id', $id)
            ->where('status', 'published')
            ->withCount('enrollments')
            ->paginate(5);

        $totalCourses = $courses->total();
        $totalStudents = $courses->sum('enrollments_count');

        // Tính doanh thu, lợi nhuận giảng viên và hệ thống
        $courseRevenues = $courses->map(function ($course) {
            $revenue = Transaction::where('course_id', $course->id)
                ->where('status', 'success')
                ->sum('amount');

            $adminRate = 0.3; // Mặc định admin nhận 30%
            $adminEarning = $revenue * $adminRate;
            $lecturerEarning = $revenue - $adminEarning;

            return [
                'id' => $course->id,
                'title' => $course->title,
                'thumbnail' => $course->thumbnail,
                'enrollments_count' => $course->enrollments_count,
                'revenue' => $revenue,
                'lecturer_earning' => $lecturerEarning,
                'admin_earning' => $adminEarning,
            ];
        });

        // Tổng cộng
        $totalRevenue = $courseRevenues->sum('revenue');
        $totalLecturerEarning = $courseRevenues->sum('lecturer_earning');
        $totalAdminEarning = $courseRevenues->sum('admin_earning');

        return view(self::PATH_VIEW . 'showlecturer', compact(
            'user', // Truyền $user vào view
            'totalCourses',
            'totalStudents',
            'totalRevenue',
            'totalLecturerEarning',
            'totalAdminEarning',
            'courseRevenues',
            'courses'
        ));
    }

    public function showStudent($id)
    {
        $user = User::with('roles')->findOrFail($id);

        // Lấy danh sách chứng chỉ đã được cấp
        $certificates = Certificate::with('course')
            ->where('user_id', $id)
            ->get();

        // Lấy lịch sử học (progress)
        $progress = Progress::with('course')
            ->where('user_id', $id)
            ->get();

        // Khóa học đang học dở (chưa hoàn thành)
        $inProgressCourses = $progress->where('status', 'in_progress');

        // Khóa học đã hoàn thành
        $completedCourses = $progress->where('status', 'completed');

        return view(self::PATH_VIEW . 'showstudent', compact(
            'user', // Truyền $user vào view
            'certificates',
            'inProgressCourses',
            'completedCourses'
        ));
    }



    public function edit(User $user)
    {
        $roles = Role::all();
        return view(self::PATH_VIEW . 'edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $oldStatus = $user->status;

        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|max:20',
            'profile_picture' => 'nullable|image|max:2048',
            'bio' => 'nullable|string|max:500',
            'google_id' => 'nullable|string',
            // 'status' => 'required|in:0,1,2',
            'password' => 'nullable|confirmed|min:8',
            'country' => 'nullable|string',
            'province' => 'nullable|string',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'gender' => 'nullable|in:male,female,other',
            'linkedin_url' => 'nullable|url',
            'website_url' => 'nullable|url',
            'certificate_file' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
            'bank_name' => 'nullable|string',
            'bank_nameUser' => 'nullable|string',
            'bank_number' => 'nullable|string|max:20',
        ], [
            // Mọi thông báo lỗi tùy chỉnh
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',

            'phone_number.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'birth_date.before_or_equal' => 'Ngày sinh không được là ngày trong tương lai.',
            'profile_picture.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'profile_picture.max' => 'Ảnh đại diện không được lớn hơn 2MB.',

            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'certificate_file.mimes' => 'Tệp chứng chỉ phải là file PDF, JPEG hoặc PNG.',
        ]);

        try {
            // Xử lý ảnh đại diện nếu có
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::delete($user->profile_picture);
                }

                $data['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            // Xử lý tệp chứng chỉ nếu có
            if ($request->hasFile('certificate_file')) {
                if ($user->certificate_file) {
                    Storage::delete($user->certificate_file);
                }

                $data['certificate_file'] = $request->file('certificate_file')->store('certificates', 'public');
            }

            // Nếu có mật khẩu mới thì mã hóa
            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            } else {
                unset($data['password']);
            }

            // Cập nhật user
            $user->update($data);

            // Gửi mail nếu trạng thái thay đổi
            if ($oldStatus != $user->status) {
                Mail::to($user->email)->send(new AccountStatusChanged($user));
            }
            return redirect()
                ->route($user->roles->contains('name', 'lecturer') ? 'admin.lecturers.index' : 'admin.students.index')
                ->with('success', 'Cập nhật user thành công!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Đã xảy ra lỗi: ' . $th->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'Xóa user thành công!');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
