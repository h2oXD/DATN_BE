<?php

namespace App\Listeners;

use App\Events\LecturerRegisterRequested;
use App\Models\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class AutoApproveLecturer implements ShouldQueue
{

    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LecturerRegisterRequested $event)
    {
        $lecturerRegister = $event->lecturerRegister;
        $user = $lecturerRegister->user;

        // Kiểm tra nếu đã là giảng viên thì không duyệt nữa
        if ($user->hasRole('lecturer')) {

            $lecturerRegister->update([
                'status' => 'rejected',
                'admin_rejection_reason' => 'Đã là giảng viên',
            ]);
            return;
        }

        // Kiểm tra nếu chưa trả lời đầy đủ thì không duyệt
        if (empty($lecturerRegister->answer1) || empty($lecturerRegister->answer2) || empty($lecturerRegister->answer3)) {
            $lecturerRegister->update([
                'status' => 'rejected',
                'admin_rejection_reason' => 'Người dùng chưa trả lời đầy đủ các câu hỏi.',
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            // Cập nhật trạng thái phê duyệt
            $lecturerRegister->update(['status' => 'approved']);

            // Thêm quyền giảng viên cho user
            $lecturerRole = Role::where('name', 'lecturer')->firstOrFail();
            $user->roles()->syncWithoutDetaching([$lecturerRole->id]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
