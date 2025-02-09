<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    const PATH_VIEW = 'admins.users.';

    // public function index()
    // {
    //     $users = User::with('roles')->latest('id')->paginate(5);
    //     // dd($users);
    //     return view(self::PATH_VIEW . 'index', compact('users'));
    // }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $role = $request->get('role');
    
        // Chỉ lấy người dùng có vai trò là 'student' hoặc 'lecturer'
        $users = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['student', 'lecturer']);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                });
            })
            ->when($role, function ($query, $role) {
                return $query->whereHas('roles', function ($query) use ($role) {
                    if ($role == 1) {
                        $query->where('role_id', 1);  // Lọc theo vai trò cụ thể nếu cần
                    } elseif ($role == 2) {
                        $query->where('role_id', 2);
                    }
                });
            })
            ->latest('id')
            ->paginate(5);
    
        return view(self::PATH_VIEW . 'index', compact('users'));
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
            'phone_number' => 'required|max:20',
            'profile_picture' => 'required|image|max:2048',
            'role' => 'required|in:lecturer,student',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',

            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'phone_number.max' => 'Số điện thoại không được vượt quá 20 ký tự.',

            'profile_picture.required' => 'Vui lòng thêm ảnh đại diện.',
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

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0
            ]);

            $role = Role::where('name', $data['role'])->first();
            $user->roles()->attach($role);

            if ($data['role'] === 'lecturer') {
                $user->roles()->attach(Role::where('name', 'student')->first());
                Lecturer::create(['user_id' => $user->id]);
            }

            Student::create(['user_id' => $user->id]);

            return redirect()->route('users.index')->with('success', 'Tạo user thành công!');

        } catch (\Throwable $th) {
            if (!empty($data['profile_picture']) && Storage::exists($data['profile_picture'])) {
                Storage::delete($data['profile_picture']);
            }
            return back()->with('success', false)->with('error', $th->getMessage());
        }
    }

    public function show(User $user)
    {
        return view(self::PATH_VIEW . 'show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view(self::PATH_VIEW . 'edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'required|max:20',
            'profile_picture' => 'nullable|image|max:2048',
            'role' => 'required|in:lecturer,student',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',

            'phone_number.required' => 'Vui lòng nhập số điện thoại.',
            'phone_number.max' => 'Số điện thoại không được vượt quá 20 ký tự.',

            'profile_picture.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'profile_picture.max' => 'Ảnh đại diện không được lớn hơn 2MB.',

        ]);

        try {
            if ($request->hasFile('profile_picture')) {
                $data['profile_picture'] = Storage::put('profile_pictures', $request->file('profile_picture'));
            }

            $user->update($data);

            $role = Role::where('name', $data['role'])->first();
            $user->roles()->sync([$role->id]);

            if ($data['role'] === 'lecturer') {
                $user->roles()->syncWithoutDetaching(Role::where('name', 'student')->first());
            }

            return redirect()->route('users.index')->with('success', 'Cập nhật user thành công!');

        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
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
