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

    /**
     * @OA\Get(
     *   path="/user/vouchers",
     *   tags={"Voucher"},
     *   summary="Lấy danh sách voucher có thể sử dụng",
     *   description="API này trả về danh sách các voucher mà người dùng chưa sử dụng và đang hoạt động.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Response(
     *     response=200,
     *     description="Danh sách voucher khả dụng",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Danh sách voucher"),
     *       @OA\Property(
     *         property="vouchers",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer", example=1),
     *           @OA\Property(property="name", type="string", example="Giảm giá 50%"),
     *           @OA\Property(property="code", type="string", example="DISCOUNT50"),
     *           @OA\Property(property="description", type="string", example="Giảm 50% cho đơn hàng trên 500.000 VND"),
     *           @OA\Property(property="type", type="string", example="percent"),
     *           @OA\Property(property="discount_price", type="integer", example=50000),
     *           @OA\Property(property="start_time", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
     *           @OA\Property(property="end_time", type="string", format="date-time", example="2024-12-31T23:59:59Z"),
     *           @OA\Property(property="count", type="integer", example=10),
     *           @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy voucher khả dụng",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Không tìm thấy voucher")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Lỗi server",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Lỗi server"),
     *       @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     *     )
     *   )
     * )
     */
    public function index()
    {
        try {

            $user_id = Auth::id();
            $usedVoucherIds = VoucherUse::where('user_id', $user_id)->pluck('voucher_id')->toArray();
            
            // Lấy danh sách các voucher mà người dùng chưa sử dụng, trong thời gian hiệu lực và số lượng > 0
            $vouchers = Voucher::whereNotIn('id', $usedVoucherIds)
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->where('count', '>', 0)
                ->where('is_active', 1)
                ->select('id', 'name', 'code', 'description', 'type', 'discount_price', 'discount_max_price', 'start_time', 'end_time', 'count', 'is_active')
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

    /**
     * @OA\Get(
     *   path="/user/voucher/{voucher_id}",
     *   tags={"Voucher"},
     *   summary="Lấy thông tin chi tiết của một voucher",
     *   description="API này trả về thông tin chi tiết của một voucher dựa trên ID nếu voucher đang hoạt động.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Parameter(
     *     name="voucher_id",
     *     in="path",
     *     required=true,
     *     description="ID của voucher cần lấy thông tin",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Chi tiết voucher",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Chi tiết voucher"),
     *       @OA\Property(
     *         property="voucher",
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Giảm giá 20%"),
     *         @OA\Property(property="code", type="string", example="DISCOUNT20"),
     *         @OA\Property(property="description", type="string", example="Giảm 20% khi mua khóa học bất kỳ"),
     *         @OA\Property(property="type", type="string", example="percent"),
     *         @OA\Property(property="discount_price", type="integer", example=20000),
     *         @OA\Property(property="start_time", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
     *         @OA\Property(property="end_time", type="string", format="date-time", example="2024-12-31T23:59:59Z"),
     *         @OA\Property(property="count", type="integer", example=5),
     *         @OA\Property(property="is_active", type="boolean", example=true)
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Voucher không tồn tại hoặc không hoạt động",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Không tìm thấy voucher")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Lỗi server",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Lỗi server"),
     *       @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     *     )
     *   )
     * )
     */
    public function show($voucher_id)
    {
        try {

            // Lấy ra voucher theo id
            $voucher = Voucher::where('id', $voucher_id)
                ->where('is_active', 1)
                ->select('id', 'name', 'code', 'description', 'type', 'discount_price', 'discount_max_price', 'start_time', 'end_time', 'count', 'is_active')
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

    /**
     * @OA\Post(
     *   path="/user/course/{course_id}/voucher/{voucher_id}/uses",
     *   tags={"Voucher"},
     *   summary="Sử dụng voucher cho khóa học",
     *   description="API này cho phép người dùng áp dụng một voucher vào một khóa học. Voucher phải hợp lệ và chưa được sử dụng trước đó.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Parameter(
     *     name="course_id",
     *     in="path",
     *     required=true,
     *     description="ID của khóa học cần áp dụng voucher",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="voucher_id",
     *     in="path",
     *     required=true,
     *     description="ID của voucher cần sử dụng",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Sử dụng voucher thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Sử dụng voucher thành công"),
     *       @OA\Property(
     *         property="voucherUse",
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="voucher_id", type="integer", example=5),
     *         @OA\Property(property="user_id", type="integer", example=10),
     *         @OA\Property(property="course_id", type="integer", example=3),
     *         @OA\Property(property="time_used", type="string", format="date-time", example="2024-02-25T10:30:00Z")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Voucher hoặc khóa học không hợp lệ",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Voucher không tồn tại hoặc không hợp lệ / Khóa học không tồn tại hoặc không hợp lệ / Voucher không khả dụng")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Lỗi server",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Lỗi server"),
     *       @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     *     )
     *   )
     * )
     */
    public function useVoucher(Request $request, $course_id, $voucher_id)
    {

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        DB::beginTransaction();

        try {
    
            $voucher = Voucher::lockForUpdate()->find($voucher_id);
            $course = Course::lockForUpdate()->find($course_id);
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

    /**
     * @OA\Get(
     *   path="/user/vouchers/history",
     *   tags={"Voucher"},
     *   summary="Lấy lịch sử sử dụng voucher của người dùng",
     *   description="API này trả về danh sách các voucher mà người dùng đã sử dụng, kèm theo thông tin khóa học liên quan.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Response(
     *     response=200,
     *     description="Lịch sử sử dụng voucher",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Lịch sử sử dụng voucher"),
     *       @OA\Property(
     *         property="history",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="voucher_use_id", type="integer", example=1),
     *           @OA\Property(
     *             property="voucher",
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=5),
     *             @OA\Property(property="name", type="string", example="Giảm giá 50%"),
     *             @OA\Property(property="code", type="string", example="DISCOUNT50"),
     *             @OA\Property(property="description", type="string", example="Giảm 50% cho đơn hàng trên 500.000 VND"),
     *             @OA\Property(property="discount_price", type="integer", example=50000),
     *             @OA\Property(property="start_time", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
     *             @OA\Property(property="end_time", type="string", format="date-time", example="2024-12-31T23:59:59Z")
     *           ),
     *           @OA\Property(
     *             property="course",
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=3),
     *             @OA\Property(property="name", type="string", example="Khóa học lập trình PHP"),
     *             @OA\Property(property="status", type="string", example="active")
     *           ),
     *           @OA\Property(property="time_used", type="string", format="date-time", example="2024-02-25T10:30:00Z")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Người dùng chưa sử dụng voucher nào",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Bạn chưa sử dụng voucher nào")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Lỗi server",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="Lỗi server"),
     *       @OA\Property(property="error", type="string", example="Chi tiết lỗi")
     *     )
     *   )
     * )
     */
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
