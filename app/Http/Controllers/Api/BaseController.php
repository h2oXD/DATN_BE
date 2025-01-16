<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Base;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function index()
    {
        return response()->json(Base::all());
    }
    public function show($id)
    {
        $base = Base::find($id);
        if (!$base) {
            return response()->json(['message' => 'ko tim thay'], 404);
        }
        return response()->json($base);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $base = Base::create($validated);
        return response()->json($base, 201);
    }

    public function update(Request $request, $id)
    {
        $base = Base::find($id);
        if (!$base) {
            return response()->json(['message' => 'ko tim thay'], 404);
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $base->update($validated);
        return response()->json($base);
    }

    public function destroy($id)
    {
        $base = Base::find($id);
        if (!$base) {
            return response()->json(['message' => 'ko tim thay'], 404);
        }
        $base->delete();
        return response()->json(['message' => 'Xoa thanh cong']);
    }
}
