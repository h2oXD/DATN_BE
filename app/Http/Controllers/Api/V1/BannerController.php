<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Symfony\Component\HttpFoundation\Response;

class BannerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/banners",
     *     summary="Lấy danh sách banner đang hoạt động",
     *     tags={"Banners"},
     *     @OA\Response(
     *         response=200,
     *         description="Lấy dữ liệu thành công",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Lấy dữ liệu thành công"),
     *             @OA\Property(
     *                 property="banners",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Banner quảng cáo"),
     *                     @OA\Property(property="image", type="string", example="https://example.com/banner.jpg")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không có banner nào đang hoạt động",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Không có banner nào đang hoạt động.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi hệ thống",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Lỗi hệ thống"),
     *             @OA\Property(property="error", type="string", example="Chi tiết lỗi hệ thống")
     *         )
     *     )
     * )
     */

    public function index()
    {
        try {
            $banners = Banner::where('status', 1)
                ->select('id', 'title', 'image')
                ->get();

            if ($banners->isEmpty()) {
                return response()->json([
                    'message' => 'Không có banner nào đang hoạt động.',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'banners' => $banners
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
