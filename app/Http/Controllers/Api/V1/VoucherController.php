<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Voucher;
use App\Models\VoucherUse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VoucherController extends Controller
{
    public function index()
    {
        try {

            $user_id = Auth::id();
            $usedVoucherIds = VoucherUse::where('user_id', $user_id)->pluck('voucher_id')->toArray();
            
            // Lấy danh sách các voucher mà người dùng chưa sử dụng
            $vouchers = Voucher::whereNotIn('id', $usedVoucherIds)
                ->where('is_active', 1)
                ->select('id', 'name', 'code', 'description', 'type', 'discount_price', 'start_time', 'end_time', 'count', 'is_active')
                ->get();

            if ($vouchers->isEmpty()) {
                
                return response()->json([
                    'message' => 'Không tìm thấy voucher'
                ], Response::HTTP_NOT_FOUND);

            } else {
                
                return response()->json([
                    'message' => 'Danh sách voucher',
                    'vouchers' => $vouchers
                ], Response::HTTP_OK);

            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($voucher_id)
    {
        try {

            // Lấy ra voucher theo id
            $voucher = Voucher::where('id', $voucher_id)
                ->where('is_active', 1)
                ->select('id', 'name', 'code', 'description', 'type', 'discount_price', 'start_time', 'end_time', 'count', 'is_active')
                ->first();
    
            if (!$voucher) {

                return response()->json([
                    'message' => 'Không tìm thấy voucher'
                ], Response::HTTP_NOT_FOUND);

            } else {

                return response()->json([
                    'message' => 'Chi tiết voucher',
                    'voucher' => $voucher
                ], Response::HTTP_OK);

            }
    
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function useVoucher(Request $request, $course_id, $voucher_id)
    {

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
    
            $voucher = Voucher::find($voucher_id);
            $course = Course::find($course_id);
            $user_id = $request->user()->id;
    
            // Kiểm tra voucher và course
            if (!$voucher || $voucher->is_active != 1 || $voucher->count <= 0) {
                return response()->json([
                    'message' => 'Voucher không tồn tại hoặc không hợp lệ'
                ], Response::HTTP_BAD_REQUEST);
            }
            if (!$course || $course->status === 'pending' || $course->status === 'draft') {
                return response()->json([
                    'message' => 'Khóa học không tồn tại hoặc không hợp lệ'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Kiểm tra xem voucher đã được sử dụng chưa
            $voucherUsed = VoucherUse::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->where('voucher_id', $voucher_id)
                ->first();
            if ($voucherUsed) {
                return response()->json([
                    'message' => 'Voucher không khả dụng'
                ], Response::HTTP_BAD_REQUEST);
            }
    
            // Giảm số lượng voucher
            $voucher->decrement('count');
            // Nếu số lượng về 0, khóa trạng thái hoạt động
            if ($voucher->count == 0) {
                $voucher->is_active = 0;
                $voucher->save();
            }
    
            // Tạo lịch sử sử dụng voucher
            $user_id = $request->user()->id;
            $voucherUse = VoucherUse::create([
                'voucher_id' => $voucher_id,
                'user_id' => $user_id,
                'course_id' => $course_id,
                'time_used' => now(),
            ]);
    
            DB::commit(); // Commit transaction
    
            return response()->json([
                'message' => 'Sử dụng voucher thành công',
                'voucherUse' => $voucherUse
            ], Response::HTTP_OK);
    
        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function history()
    {
        try {

            $user_id = Auth::id();

            // Lấy danh sách các bản ghi voucher_uses của người dùng
            $voucherUses = VoucherUse::where('user_id', $user_id)
                ->with('voucher', 'course')
                ->get();

            if ($voucherUses->isEmpty()) {
                return response()->json([
                    'message' => 'Bạn chưa sử dụng voucher nào'
                ], Response::HTTP_NOT_FOUND);
            }

            // Tạo mảng kết quả với thông tin chi tiết
            $history = $voucherUses->map(function ($voucherUse) {
                return [
                    'voucher_use_id' => $voucherUse->id,
                    'voucher' => $voucherUse->voucher,
                    'course' => $voucherUse->course,
                    'time_used' => $voucherUse->time_used,
                ];
            });

            return response()->json([
                'message' => 'Lịch sử sử dụng voucher',
                'history' => $history
            ], Response::HTTP_OK);
    
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
