<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LecturerRegister;
use Illuminate\Http\Request;

class LecturerRegisterController extends Controller
{
    const PATH_VIEW = 'admins.lecturer_registers.';

    public function index(Request $request)
    {
        $lecturerRegisters = LecturerRegister::with('user');
        if ($request->has('status') && $request->status !== '') {
            $lecturerRegisters->where('status', $request->status);
        }

        $lecturerRegisters = $lecturerRegisters->paginate(10);
        return view(self::PATH_VIEW . 'index', compact('lecturerRegisters'));
    }

    public function show(LecturerRegister $lecturerRegister)
    {
        return view(self::PATH_VIEW . 'show', compact('lecturerRegister'));
    }
}