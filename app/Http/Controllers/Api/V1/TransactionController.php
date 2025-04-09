<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    // Danh sách khóa học đã mua của học viên
    public function courseList(Request $request)
    {
        try {

            $user_id = $request->user()->id;

            $transactions = Transaction::with(['course.user'])
                                        ->where('status', 'success')
                                        ->where('user_id', $user_id)
                                        ->get();

            // Kiểm tra xem người dùng có tồn tại không
            if (!$user_id) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy người dùng này.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem có tồn tại danh sách khóa học không
            if ($transactions->isEmpty()) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => 'Không có khóa học nào đã được mua.'
                ], Response::HTTP_OK);
            }

            return response()->json([
                'status'                    => 'success',
                'Danh sách khóa học đã mua' => $transactions
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Danh sách khóa học đã bán của giảng viên
    public function sellList(Request $request)
    {
        try {

            $user_id = $request->user()->id;

            $sell_list = Course::with(['transactions' => function ($query) {
                                    $query->where('status', 'success');
                                }])
                                ->where('user_id', $user_id)
                                ->where('status', 'published')
                                ->get();

            // Xử lý tính doanh thu cho từng khóa học
            $sell_list_with_revenue = $sell_list->map(function ($course) {
                $revenue = $course->transactions->sum('amount');

                return [
                    'ID'            => $course->id,
                    'Tên khóa học'  => $course->title,
                    'Doanh thu'     => $revenue,
                    'Lợi nhuận'     => ($revenue/100) * 70,
                    'Số lượt mua'   => $course->transactions->count(),
                ];
            });

            // Kiểm tra xem người dùng có tồn tại không
            if (!$user_id) {
                return response()->json([
                    'status'    => 'error',
                    'message'   => 'Không tìm thấy người dùng này.'
                ], Response::HTTP_NOT_FOUND);
            }
            // Kiểm tra xem có tồn tại danh sách khóa học không
            if ($sell_list->isEmpty()) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => 'Không có khóa học nào đang được bán.'
                ], Response::HTTP_OK);
            }

            return response()->json([
                'status'                    => 'success',
                'Danh sách khóa học đã bán' => $sell_list,
                'Tổng doanh thu'            => $sell_list_with_revenue
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi server',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Danh sách học viên theo khóa học đã bán

}
