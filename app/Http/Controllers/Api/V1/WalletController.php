<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 3;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get(
     *   path="/user/wallets",
     *   tags={"Wallet"},
     *   summary="Lấy thông tin ví của người dùng",
     *   description="API này trả về thông tin chi tiết về ví của người dùng đã xác thực.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Response(
     *     response=200,
     *     description="Thành công - Trả về thông tin ví",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(
     *         property="wallet",
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="user_id", type="integer", example=2),
     *         @OA\Property(property="balance", type="number", example=999000),
     *         @OA\Property(property="transaction_history", type="string", example=null),
     *         @OA\Property(property="created_at", type="string", example=null),
     *         @OA\Property(property="updated_at", type="string", example=null),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy ví",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy ví của người dùng này.")
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
    public function show(Request $request)
    {
        try {

            $wallet = $request->user()->wallet;

            // Kiểm tra xem ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng này.'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'status' => 'success',
                'wallet' => $wallet
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *   path="/user/wallets",
     *   tags={"Wallet"},
     *   summary="Cập nhật thông tin ví của người dùng",
     *   description="API này cho phép người dùng cập nhật số dư trong ví của mình.",
     *   security={{"BearerAuth": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="balance", type="integer", example=15000, description="Số dư mới của ví")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ví được cập nhật",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="Ví được cập nhật"),
     *       @OA\Property(
     *         property="wallet",
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=1),
     *         @OA\Property(property="user_id", type="integer", example=2),
     *         @OA\Property(property="balance", type="number", example=99000),
     *         @OA\Property(property="transaction_history", type="string", example=null),
     *         @OA\Property(property="created_at", type="string", example=null),
     *         @OA\Property(property="updated_at", type="string", example=null),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Lỗi validation",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="errors", type="object", description="Các lỗi validation")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy ví",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy ví của người dùng này.")
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
    public function update(Request $request)
    {
        try {

            $wallet = $request->user()->wallet;

            // Kiểm tra xem ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng này.'
                ], Response::HTTP_NOT_FOUND);
            }

            // Validate dữ liệu truyền vào
            $validator = Validator::make($request->all(), [
                'balance' => 'required|int',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }
            $data = $request->all();

            $wallet->update($data);

            return response()->json([
                'status' => 'Ví được cập nhật',
                'wallet' => $wallet
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * @OA\Post(
     *   path="/user/courses/{course_id}/wallet-payment",
     *   tags={"Wallet"},
     *   summary="Thanh toán khóa học bằng ví",
     *   description="API này cho phép người dùng thanh toán khóa học bằng số dư trong ví.",
     *   security={{"BearerAuth": {}}},
     *   @OA\Parameter(
     *       name="course_id",
     *       in="path",
     *       description="ID của khóa học",
     *       required=true,
     *       @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="amount", type="integer", example=10000, description="Số tiền cần thanh toán")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Thanh toán thành công",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="message", type="string", example="Thanh toán thành công")
     *     )
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="Lỗi validation hoặc số dư không đủ",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="errors", type="object", description="Các lỗi validation"),
     *       @OA\Property(property="error", type="string", example="Số dư ví không đủ")
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Không tìm thấy ví hoặc khóa học",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Không tìm thấy khóa học cần thanh toán")
     *     )
     *   ),
     *   @OA\Response(
     *     response=423,
     *     description="Khóa học đã sở hữu hoặc trạng thái khóa học không hợp lệ",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="status", type="string", example="error"),
     *       @OA\Property(property="message", type="string", example="Khóa học đã sở hữu không cần thanh toán")
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
    public function payment(Request $request, $course_id)
    {
        try {

            $wallet = $request->user()->wallet;
            $own_course = $request->user()->courses()->find($course_id);
            $course = Course::findOrFail($course_id);

            // Kiểm tra ví có tồn tại hay không
            if (!$wallet) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy ví của người dùng này'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra khóa học đã được sở hữu chưa
            if ($own_course) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Khóa học đã sở hữu không cần thanh toán'
                ], Response::HTTP_LOCKED);
            }
            // Kiểm tra khóa học có tồn tại không
            if (!$course) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy khóa học cần thanh toán'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra trạng thái của khóa học có hợp lệ không
            if ($course->status === "pending" || $course->status === "draft") {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Trạng thái khóa học không hợp lệ'
                ], Response::HTTP_LOCKED);
            }
            // Kiểm tra giao dịch có bị trùng lặp không
            $existingTransaction = Transaction::where('user_id', $request->user()->id)
                ->where('course_id', $course_id)
                ->whereIn('status', ['pending', 'success'])
                ->first();
            if ($existingTransaction) {
                return response()->json([
                    'error' => 'Giao dịch thất bại'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Kiểm tra dữ liệu truyền lên
            $validator = Validator::make($request->all(), [
                'amount' => 'required|int',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Kiểm tra số dư ví người dùng
            if ($wallet->balance < $request->amount) {
                return response()->json([
                    'error' => 'Số dư ví không đủ'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Trừ tiền trong ví, ghi lịch sử giao dịch vào database và tham gia khóa học
            $wallet->decrement('balance', $request->amount);
            $wallet->update([
                'transaction_history' => [
                    'Loại giao dịch'        => 'Thanh toán khóa học',
                    'Số tiền thanh toán'    => $request->amount,
                    'Số dư'                 => $wallet->balance,
                    'Ngày giao dịch'        => Carbon::now('Asia/Ho_Chi_Minh')
                ]
            ]);
            Transaction::create([
                'user_id'           => $request->user()->id,
                'course_id'         => $course_id,
                'amount'            => $request->amount,
                'payment_method'    => 'wallet',
                'status'            => 'success',
                'transaction_date'  => Carbon::now('Asia/Ho_Chi_Minh')
            ]);
            Enrollment::create([
                'user_id'       => $request->user()->id,
                'course_id'     => $course_id,
                'status'        => 'active',
                'enrolled_at'   => Carbon::now('Asia/Ho_Chi_Minh')
            ]);
            Progress::create([
                'user_id'           => $request->user()->id,
                'course_id'         => $course_id,
                'status'            => 'in_progress',
                'progress_percent'  => 0
            ]);

            return response()->json([
                'status'    => 'success',
                'message'   => 'Thanh toán thành công'
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Lỗi server',
                'error'     => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
