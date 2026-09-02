<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment; 

class CommentController extends Controller
{
    public function store(Request $request, $novel_id)
    {
        $request->validate([
            'comment_text' => 'required|max:1000',
            'chapter_id' => 'nullable' // ต้องอนุญาตให้เป็น null ได้
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'novel_id' => $novel_id,
            // สำคัญ: ถ้าไม่มีการส่ง chapter_id มาจากฟอร์ม ต้องให้เป็น null
            'chapter_id' => $request->has('chapter_id') ? $request->chapter_id : null,
            'comment_text' => $request->comment_text,
        ]);

        return back()->with('success', 'แสดงความคิดเห็นเรียบร้อยแล้ว');
    }

    public function destroy(Comment $comment)
    {
        // เช็คว่าคนลบคือเจ้าของคอมเมนต์ หรือเป็น Admin (role_id <= 2)
        if (auth()->id() === $comment->user_id || auth()->user()->role_id <= 2) {
            $comment->delete();
            return back()->with('success', 'ลบคอมเมนต์เรียบร้อยแล้ว');
        }

        return back()->with('error', 'คุณไม่มีสิทธิ์ลบคอมเมนต์นี้');
    }
}
