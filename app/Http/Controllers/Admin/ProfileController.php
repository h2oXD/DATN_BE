<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    const PATH_VIEW = 'admins.profiles.'; // Định nghĩa đường dẫn view giống như CourseController

    public function edit()
    {
        $user = Auth::user();
        return view(self::PATH_VIEW . 'edit', compact('user')); // Sử dụng compact như trong CourseController
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8',
            'profile_picture' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên đầy đủ.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã được sử dụng.',
            'profile_picture.image' => 'File phải là hình ảnh.',
        ]);

        try {
            $user = Auth::user();
            $userData = [
                'name' => $data['name'],
                'phone_number' => $data['phone'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            if ($request->hasFile('profile_picture')) {
                $image = $request->file('profile_picture');
                $imageContents = file_get_contents($image->getRealPath());
                $imageHash = sha1($imageContents); // Tạo hash dựa trên nội dung ảnh
                $imageExtension = $image->getClientOriginalExtension();
                $imageName = "{$imageHash}.{$imageExtension}";
                $imagePath = "profile_pictures/{$imageName}";

                if (!Storage::disk('public')->exists($imagePath)) {
                    // Ảnh chưa tồn tại -> Lưu ảnh mới
                    $image->storeAs('profile_pictures', $imageName, 'public');

                    // Xóa ảnh cũ nếu có
                    if ($user->profile_picture) {
                        $oldImagePath = str_replace('storage/', '', $user->profile_picture);
                        if (Storage::disk('public')->exists($oldImagePath)) {
                            Storage::disk('public')->delete($oldImagePath);
                        }
                    }
                }

                // Cập nhật đường dẫn ảnh mới cho user
                $userData['profile_picture'] = "storage/{$imagePath}";
            }

            $user->update($userData);

            return Redirect::route('admin.profiles.edit')->with('success', 'Cập nhật hồ sơ thành công!');
        } catch (\Throwable $th) {
            return Redirect::back()->with('error', $th->getMessage());
        }
    }
}