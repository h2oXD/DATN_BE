<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    const PATH_VIEW = 'admins.profile.'; // Định nghĩa đường dẫn view giống như CourseController

    public function edit()
    {
        $user = Auth::user();
        return view(self::PATH_VIEW . 'edit', compact('user')); // Sử dụng compact như trong CourseController
    }

    public function update(Request $request)
    {
        // Xác thực dữ liệu đầu vào với thông báo tùy chỉnh, chỉ sử dụng các cột hiện có
        $data = $request->validate([
            'name' => 'required|string|max:255', // Sử dụng cột name để lưu full name
            'phone' => 'required|string|max:20', // Ánh xạ sang phone_number
            'email' => 'required|email|unique:users,email,' . Auth::id(), // Kiểm tra email unique, bỏ qua bản ghi hiện tại
            'password' => 'nullable|string|min:8', // Password optional, tối thiểu 8 ký tự
            'profile_picture' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // Hình ảnh, tối đa 2MB
        ], [
            'name.required' => 'Vui lòng nhập tên đầy đủ.',
            'name.max' => 'Tên đầy đủ không được vượt quá 255 ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'profile_picture.image' => 'File phải là hình ảnh.',
            'profile_picture.mimes' => 'Hình ảnh phải có định dạng PNG, JPG, hoặc JPEG.',
            'profile_picture.max' => 'Hình ảnh không được lớn hơn 2MB.',
        ]);

        try {
            // Lấy người dùng hiện tại
            $user = Auth::user();

            // Chuẩn bị dữ liệu để cập nhật
            $userData = [
                'name' => $data['name'],
                'phone_number' => $data['phone'], // Ánh xạ phone sang phone_number
                'email' => $data['email'],
            ];

            // Xử lý mật khẩu nếu có
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            // Xử lý profile_picture nếu có
            if ($request->hasFile('profile_picture')) {
                // Xóa ảnh cũ nếu tồn tại
                if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
                    unlink(public_path($user->profile_picture));
                }

                // Lưu ảnh mới vào thư mục public/profile_pictures/
                $image = $request->file('profile_picture');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('profile_pictures'), $imageName);

                // Lưu đường dẫn vào database
                $userData['profile_picture'] = 'profile_pictures/' . $imageName;
            }
            // Cập nhật người dùng
            $user->update($userData);

            return Redirect::route('admin.admins.profile.edit')->with('success', 'Cập nhật hồ sơ thành công!');
        } catch (\Throwable $th) {
            return Redirect::back()->with('error', $th->getMessage());
        }
    }
}
