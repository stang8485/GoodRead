<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novel;   
use App\Models\Chapter; 
use App\Models\CoinTopup; 
use App\Models\ChapterPurchase;
use App\Models\CoinTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\NovelController;
use Illuminate\Support\Facades\DB;

class ChapterController extends Controller
{
    // เพิ่มฟังก์ชันสำหรับแสดงหน้าฟอร์ม
    public function create($target_novel_id) 
    {
        
        // ตรวจสอบสิทธิ์ (เจ้าของเรื่อง หรือ แอดมิน)
        // if (auth()->id() !== $novel->author_id && auth()->user()->role_id > 2) {
        //     abort(403);
        // }
        $novel = Novel::findOrFail($target_novel_id);
        return view('chapters.create', compact('novel'));
        // return "Route is working! ID is: " . $target_novel_id;
    }

    // ฟังก์ชันบันทึกข้อมูล 
    public function store(Request $request, $target_novel_id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'chapter_number' => 'required|integer',
            'price' => 'required|integer|min:0',
        ]);
        $novel = Novel::findOrFail($target_novel_id);

        //  3 ตอนแรกฟรี
        $chapterNumber = $request->chapter_number;
        $price = $request->price;
        $isFree = false;

        if ($chapterNumber <= 3) {
            $price = 0; // บังคับราคาเป็น 0
            $isFree = true; // บังคับสถานะเป็นฟรี
        } else {
            // ถ้าตอนที่ 4 เป็นต้นไป ให้ดูจากราคา
            $isFree = ($price == 0); 
        }
        

        $novel->chapters()->create([
        'title' => $request->title,
        'content' => $request->content,
        'chapter_number' => $chapterNumber, 
        'price' => $price, 
        'is_free' => $isFree, 
        'status' => 'published',
    ]);

        return redirect()->route('novels.show', $novel->slug)->with('success', 'เพิ่มตอนใหม่เรียบร้อยแล้ว!');
    }

    public function show($novelId, $chapterId)
    {
        // ดึงข้อมูลตอนนิยายและนิยายต้นสังกัด
        $chapter = Chapter::with('novel')->findOrFail($chapterId);
        $novel = $chapter->novel;
        $user = auth()->user();

        // เช็คสิทธิ์พื้นฐาน (อ่านฟรีได้เลยถ้า...)
        // - เป็น 3 ตอนแรก หรือตั้งค่า is_free เป็น true
        // - เป็นเจ้าของนิยาย (author_id)
        // - เป็น Admin/Staff (role_id 1 หรือ 2)
        $isFree = $chapter->chapter_number <= 3 || $chapter->is_free;
        $isStaffOrAuthor = $user && ($user->id === $novel->author_id || $user->role_id <= 2);
        
        // เช็คว่า User เคยซื้อตอนนี้ไปหรือยัง
        $isPurchased = $user && $user->hasPurchased($chapter->id);

        // --- กฎการกั้นเนื้อหา ---
        if ($isFree || $isStaffOrAuthor || $isPurchased) {
            // ถ้าเข้าเงื่อนไขใดเงื่อนไขหนึ่ง ให้อ่านได้ปกติ
            $chapter->increment('view_count'); // เพิ่มยอดวิว
            return view('chapters.show', [
                'novel' => $novel,
                'chapter' => $chapter,
                'needsPurchase' => false // บอก Blade ว่าไม่ต้องแสดงปุ่มซื้อ
            ]);
        }

        // ถ้าไม่เข้าเงื่อนไขเลย 
        // ให้ส่งไปหน้าเดิม แต่ส่งตัวแปร needsPurchase เป็น true เพื่อให้ Blade แสดงปุ่มซื้อแทนเนื้อหา
        return view('chapters.show', [
            'novel' => $novel,
            'chapter' => $chapter,
            'needsPurchase' => true // ตัวแปรสำคัญที่จะกั้นเนื้อหาในหน้าอ่าน
        ]);
    }

    // หน้าฟอร์มแก้ไขตอน
    public function edit($novel_id, $chapter_id)
    {
        $novel = Novel::findOrFail($novel_id);
        $chapter = Chapter::findOrFail($chapter_id);
        // ตรวจสอบสิทธิ์เจ้าของนิยาย
        if (auth()->id() !== $novel->author_id && auth()->user()->role_id > 2) {
            abort(403);
        }
        return view('chapters.edit', compact('novel', 'chapter'));
    }

    // ฟังก์ชันอัปเดตข้อมูลตอน
    public function update(Request $request, $novel_id, $chapter_id)
    {
        $novel = Novel::findOrFail($novel_id);
        $chapter = Chapter::findOrFail($chapter_id);
        if (auth()->id() !== $novel->author_id && auth()->user()->role_id > 2) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'chapter_number' => 'required|integer',
            'price' => 'required|integer|min:0',
        ]);

        $chapter->update([
            'title' => $request->title,
            'content' => $request->content,
            'chapter_number' => $request->chapter_number,
            'price' => $request->price,
            'is_free' => $request->price == 0 ? true : false,
        ]);

        return redirect()->route('novels.show', [($novel->id)])
                        ->with('success', 'แก้ไขตอนเรียบร้อยแล้ว');
    }

    // ฟังก์ชันลบตอน
    public function destroy($novel_id, $chapter_id)
    {
        $novel = Novel::findOrFail($novel_id);
        $chapter = Chapter::findOrFail($chapter_id);
        if (auth()->id() !== $novel->author_id && auth()->user()->role_id > 2) {
            abort(403);
        }

        $chapter->delete();

        return redirect()->route('novels.show', $novel->slug)
                        ->with('success', 'ลบตอนเรียบร้อยแล้ว');
    }

    public function purchase(Chapter $chapter)
    {
        $user = auth()->user();

        try {
            DB::transaction(function () use ($user, $chapter) {
                $user = \App\Models\User::lockForUpdate()->findOrFail($user->id);
                if (ChapterPurchase::where('user_id', $user->id)->where('chapter_id', $chapter->id)->exists()) {
                    throw new \RuntimeException('คุณเคยซื้อตอนนี้ไปแล้ว');
                }
                if ($user->coin_balance < $chapter->price) {
                    throw new \RuntimeException('เหรียญไม่พอ กรุณาเติมเหรียญ');
                }

                $user->decrement('coin_balance', $chapter->price);
                $purchase = ChapterPurchase::create([
                'user_id' => $user->id,
                'chapter_id' => $chapter->id,
                'price_paid' => $chapter->price
            ]);
                $user->refresh();
                CoinTransaction::create([
                    'user_id' => $user->id, 'type' => 'chapter_purchase',
                    'coins_change' => -$chapter->price, 'balance_after' => $user->coin_balance,
                    'chapter_purchase_id' => $purchase->id,
                ]);
            });
        } catch (\RuntimeException $e) {
            return $e->getMessage() === 'เหรียญไม่พอ กรุณาเติมเหรียญ'
                ? redirect()->route('topup.index')->with('error', $e->getMessage())
                : back()->with('info', $e->getMessage());
        }

        return back()->with('success', 'ซื้อตอนนิยายสำเร็จแล้ว!');
    }

}
