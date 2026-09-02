<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog; 
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ดูรายชื่อผู้ใช้
    public function index() {
        $users = User::with('role')->get();
        return view('admin.users.index', compact('users'));
    }

    // ดูประวัติการเข้า-ออก (Log)
    public function activityLogs() {
        $logs = ActivityLog::with('user')
                ->whereIn('action', ['login', 'logout'])
                ->latest()
                ->paginate(20);
        return view('admin.logs.index', compact('logs'));
    }

    // ลบผู้ใช้ (เงื่อนไขห้ามลบ Super Admin)
    public function destroy(User $user) {
        if ($user->role_id == 1) {
            return back()->with('error', 'ไม่สามารถลบ Super Admin ได้!');
        }

        $user->delete();
        return back()->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }
}