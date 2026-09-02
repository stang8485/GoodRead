<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novel;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    // 1. ฟังก์ชันกดปุ่มติดตาม / ยกเลิกติดตาม
    public function toggle($novelId)
    {
        $user = Auth::user();

        // toggle() จะเพิ่มเข้าชั้นหนังสือถ้ายังไม่มีหรือลบออกถ้ามีอยู่แล้ว
        $user->bookmarkedNovels()->toggle($novelId);

        return back()->with('success', 'อัปเดตชั้นหนังสือเรียบร้อยแล้ว!');
    }

    // 2. ฟังก์ชันแสดงหน้ารายการชั้นหนังสือของฉัน
    public function index()
    {
        $books = Auth::user()->bookmarkedNovels()->with(['author', 'category'])->latest('bookmarks.created_at')->paginate(12);

        return view('bookshelf.index', compact('books'));
    }
}