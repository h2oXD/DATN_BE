<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBaseController extends Controller
{
    public $model;
    public $urlbase = 'users.';
    public $fieldImage;
    public $folderImage;
    public $columns = [];

    public function __construct()
    {
        $this->model = app()->make($this->model);
    }

    public function index()
    {
        $data = $this->model->paginate(5);

        return response()->json([
            'data' => $data,
            'columns' => $this->columns
        ]);
    }

    public function store(Request $request)
    {
        $this->validateStore($request);

        $model = new $this->model;
        $model->fill($request->except([$this->fieldImage]));

        if ($request->hasFile($this->fieldImage)) {
            $tmpPath = Storage::put($this->folderImage, $request->{$this->fieldImage});
            $model->{$this->fieldImage} = 'storage/' . $tmpPath;
        }

        $model->save();

        return response()->json([
            'message' => 'Thao tác thành công',
            'data' => $model
        ]);
    }

    public function show(string $id)
    {
        $model = $this->model->findOrFail($id);
        return response()->json($model);
    }

    public function update(Request $request, string $id)
    {
        $this->validateUpdate($request);

        $model = $this->model->findOrFail($id);
        $model->fill($request->except([$this->fieldImage]));

        if ($request->hasFile($this->fieldImage)) {
            $oldImage = $model->{$this->fieldImage};
            $tmpPath = Storage::put($this->folderImage, $request->{$this->fieldImage});
            $model->{$this->fieldImage} = 'storage/' . $tmpPath;

            // Xóa ảnh cũ nếu có
            if ($oldImage) {
                $oldImagePath = str_replace('storage/', '', $oldImage);
                Storage::delete($oldImagePath);
            }
        }

        $model->save();

        return response()->json([
            'message' => 'Thao tác thành công',
            'data' => $model
        ]);
    }

    public function destroy(string $id)
    {
        $model = $this->model->findOrFail($id);
        $imagePath = $model->{$this->fieldImage};

        $model->delete();

        if (Storage::exists($imagePath)) {
            $imagePath = str_replace('storage/', '', $imagePath);
            Storage::delete($imagePath);
        }

        return response()->json(['message' => 'Xóa thành công']);
    }

    public function import()
    {
        // Xử lý nhập khẩu nếu cần thiết
    }

    public function export()
    {
        // Xử lý xuất khẩu nếu cần thiết
    }

    public function validateStore(Request $request)
    {
        // Xử lý validate khi thêm mới
    }

    public function validateUpdate(Request $request)
    {
        // Xử lý validate khi cập nhật
    }
}
