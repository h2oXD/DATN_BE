<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBaseController extends Controller
{
    protected $model;
    protected $viewPath;
    protected $uploadPath;
    protected $fieldImage = 'image'; // Biến dùng chung cho tên trường ảnh

    public function __construct()
    {
        // Định nghĩa các thuộc tính trong controller con
        // Ví dụ: 
        // $this->model = Product::class;
        // $this->viewPath = 'admin.products.';
        // $this->uploadPath = 'images/products'; 
    }

    public function index()
    {
        $items = $this->model::paginate(15);
        return view($this->viewPath . __FUNCTION__, compact('items'));
    }

    public function create()
    {
        return view($this->viewPath . __FUNCTION__);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->validateStore());

        if ($request->hasFile($this->fieldImage)) {
            $data[$this->fieldImage] = $this->uploadFile($request->file($this->fieldImage));
        }

        $this->model::create($data);

        return back()->with('success', 'Thêm mới thành công!');
    }

    public function show($id)
    {
        $item = $this->model::findOrFail($id);
        return view($this->viewPath . __FUNCTION__, compact('item'));
    }

    public function edit($id)
    {
        $item = $this->model::findOrFail($id);
        return view($this->viewPath . __FUNCTION__, compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = $this->model::findOrFail($id);
        $data = $request->validate($this->validateUpdate());

        if ($request->hasFile($this->fieldImage)) {
            $data[$this->fieldImage] = $this->uploadFile($request->file($this->fieldImage));
        }

        $item->update($data);

        // Xóa ảnh cũ sau khi update thành công
        if ($request->hasFile($this->fieldImage) && $item->{$this->fieldImage}) {
            Storage::delete($item->{$this->fieldImage});
        }

        return back()->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $item = $this->model::findOrFail($id);
        $imagePath = $item->{$this->fieldImage}; // Lưu đường dẫn ảnh

        $item->delete();

        // Xóa ảnh sau khi xóa bản ghi thành công
        if ($imagePath) {
            Storage::delete($imagePath);
        }

        return back()->with('success', 'Xóa thành công!');
    }

    protected function uploadFile($file)
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs($this->uploadPath, $fileName, 'public');
    }

    public function validateStore()
    {
        return([

        ]);
    }
    public function validateUpdate()
    {
        return([

        ]);
    }
}
