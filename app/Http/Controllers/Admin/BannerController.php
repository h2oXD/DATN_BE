<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    protected const VIEW_PATH = 'admins.banners.';

    public function index()
    {
        $banners = Banner::latest()->paginate(10);
        return view(self::VIEW_PATH . 'index', compact('banners'));
    }

    public function create()
    {
        return view(self::VIEW_PATH . 'create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',//|dimensions:width=1200,height=600
                'link' => 'nullable|string|max:255',
                'status' => 'required|boolean',
            ],

            [
                'title.required' => 'Vui lòng đặt tên tên cho banner',
                'image.required' => 'Vui lòng nhập ảnh',
                'image.image' => 'Vui lòng nhập đúng định dạng',
                // 'image.dimensions' => 'Vui lòng nhập đúng định dạng 1200-600',
            ]
        );

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'image' => $imagePath,
            'link' => $request->link,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm thành công.');
    }

    public function edit(Banner $banner)
    {
        return view(self::VIEW_PATH . 'edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate(
            [
                'title' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'link' => 'nullable|string|max:255',
                'status' => 'required|boolean',
            ],

            [
                'title.required' => 'Vui lòng đặt tên tên cho banner',
                'image.required' => 'Vui lòng nhập ảnh',
                'image.image' => 'Vui lòng nhập đúng định dạng',
            ]
        );

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::delete('public/' . $banner->image);
            }
            $imagePath = $request->file('image')->store('banners', 'public');
            $banner->image = $imagePath;
        }

        $banner->title = $request->title;
        $banner->link = $request->link;
        $banner->status = $request->status;
        $banner->save();

        return redirect()->route('admin.banners.index')->with('success', 'Sửa thành công.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::delete('public/' . $banner->image);
        }

        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Xóa thành công.');
    }
}
