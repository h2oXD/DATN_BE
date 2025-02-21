<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LecturerRegister;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LecturerRegisterController extends Controller
{
    const PATH_VIEW = 'admins.lecturer_registers.';

    public function index(Request $request)
    {
        $lecturerRegisters = LecturerRegister::with('user');
        if ($request->has('status') && $request->status !== '') {
            $lecturerRegisters->where('status', $request->status);
        }


        $lecturerRegisters = $lecturerRegisters->paginate(10);
        // dd($lecturerRegisters);
        return view(self::PATH_VIEW . 'index', compact('lecturerRegisters'));
    }

    public function show($id)
    {

        $lecturerRegister = LecturerRegister::findOrFail($id);
        return view(self::PATH_VIEW . 'show', compact('lecturerRegister'));
    }

    public function approve($id)
    {
        $lecturerRegister = LecturerRegister::with('user')->findOrFail($id);
        $user = $lecturerRegister->user;

        if ($user->hasRole('lecturer')) {
            return Redirect::back()->with('error', 'Người dùng đã là giảng viên.');
        }

        if (empty($lecturerRegister->answer1) || empty($lecturerRegister->answer2) || empty($lecturerRegister->answer3)) {
            return Redirect::back()->with('error', 'Người dùng chưa trả lời đầy đủ các câu hỏi.');
        }

        try {
            DB::beginTransaction();

           
            $lecturerRegister->update(['status' => 'approved']);

           
            $lecturerRole = Role::where('name', 'lecturer')->firstOrFail();

            
            $user->roles()->syncWithoutDetaching([$lecturerRole->id]);

            DB::commit();

            return Redirect::route('admin.lecturer_registers.index')->with('success', 'Người dùng đã được phê duyệt làm giảng viên.');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Đã xảy ra lỗi khi phê duyệt giảng viên: ' . $e->getMessage());
        }
    }


    public function reject(Request $request, $id)
    {
        $lecturerRegister = LecturerRegister::with('user')->findOrFail($id);

        $request->validate([
            'admin_rejection_reason' => 'required|string|max:255',
        ]);

        try {
            // Cập nhật trạng thái và lý do từ chối trong bảng lecturer_register
            $lecturerRegister->update([
                'status' => 'rejected',
                'admin_rejection_reason' => $request->admin_rejection_reason,
            ]);

            return Redirect::route('admin.lecturer_registers.index')
                ->with('success', 'Yêu cầu làm giảng viên đã bị từ chối.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Đã xảy ra lỗi khi từ chối yêu cầu: ' . $e->getMessage());
        }
    }
}
