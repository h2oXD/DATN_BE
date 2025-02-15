<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashBoardController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    }
    public function dashboard()
    {
        return view('admins.dashboards.dash-board');
    }
}
