<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'data' => $categories
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:2'],
                'description' => ['nullable', 'string', 'max:255'],
                'parent_id' => ['nullable', Rule::exists('categories', 'id')],
                'image' => ['nullable', Rule::file()->max(2048)->types(['jpg', 'jpeg', 'png'])]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $validator->validated();

            if ($data['parent_id']) {
                $parent = Category::find($data['parent_id']);
                if ($parent && $parent->parent_id !== null) {
                    return response()->json([
                        'message' => 'Dữ liệu không hợp lệ',
                        'errors' => [
                            'parent_id' => 'Không thể thêm danh mục vào danh mục con của một con danh mục khác'
                        ]
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $slug = Str::slug($data['name']);
            $existingCategory = Category::where('slug', $slug)->first();

            if ($existingCategory) {
                $slug = $slug . '-' . time();
            }

            $data['slug'] = $slug;

            $category = Category::create($data);

            return response()->json([
                'message' => 'Thêm mới danh mục thành công',
                'data' => $category
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = Category::with('children')->find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Không tìm danh mục',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'message' => 'Lấy dữ liệu thành công',
                'data' => $category
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Không tìm danh mục',
                ], Response::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:2'],
                'description' => ['nullable', 'string', 'max:255'],
                'parent_id' => ['nullable', Rule::exists('categories', 'id')],
                'image' => ['nullable', Rule::file()->max(2048)->types(['jpg', 'jpeg', 'png'])]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $validator->validated();

            if ($data['parent_id']) {
                $parent = Category::find($data['parent_id']);
                if ($parent && $parent->parent_id !== null) {
                    return response()->json([
                        'message' => 'Dữ liệu không hợp lệ',
                        'errors' => [
                            'parent_id' => 'Không thể thêm danh mục vào danh mục con của một con danh mục khác'
                        ]
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            $slug = Str::slug($data['name']);
            $existingCategory = Category::where('slug', $slug)->first();

            if ($existingCategory) {
                $slug = $slug . '-' . time();
            }

            $data['slug'] = $slug;

            $category->udpate($data);

            return response()->json([
                'message' => 'Cập nhật danh mục thành công',
                'data' => $category
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Không tìm danh mục',
            ], Response::HTTP_NOT_FOUND);
        }

        // Lấy danh sách các khóa học liên quan
        $courses = $category->courses;

        // Xử lý các khóa học (ví dụ: gán category_id = null)
        foreach ($courses as $course) {
            $course->category_id = null; // Hoặc một giá trị khác phù hợp
            $course->save();
        }

        $category->delete();

        return response()->noContent();
    }
}
