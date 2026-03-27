<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Models\Admin;
use Carbon\Carbon;
use App\Models\Attendance;


class AdminController extends Controller
{
    public function create()
    {
        return view('admin.auth.login');
    }

    public function store(Request $request, LoginResponse $loginResponse)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'login_error' => ['管理者のログイン情報が正しくありません'],
            ]);
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));
        $request->session()->regenerate();

        return $loginResponse->toResponse($request);
    }

    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function attendanceList()
    {
        return view('admin.attendance.list');
    }
}
