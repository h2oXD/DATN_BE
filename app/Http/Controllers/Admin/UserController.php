<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    const PATH_VIEW = 'admins.users.';

    public function index()
    {
        $users = User::with('roles')->latest('id')->paginate(5);
        // dd($users);
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
            'phone_number' => 'nullable|max:20',
            'profile_picture' => 'nullable|image|max:2048',
            'role' => 'required|in:lecturer,student',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            if ($request->hasFile('profile_picture')) {
                $data['profile_picture'] = Storage::put('profile_pictures', $request->file('profile_picture'));
            }

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

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
            'phone_number' => 'nullable|max:20',
            'profile_picture' => 'nullable|image|max:2048',
            'role' => 'required|in:lecturer,student',
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
