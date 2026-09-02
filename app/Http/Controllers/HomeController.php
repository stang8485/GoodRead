<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Novel;   
use App\Models\Chapter; 
use App\Models\CoinTopup; 
use App\Models\Category; 
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\NovelController;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. ดึงหมวดหมู่พร้อมนิยายเตรียมไว้เสมอ (แก้ปัญหา Undefined variable $categories)
        $categories = Category::whereHas('novels')
            ->with(['novels' => function ($query) {
                $query->with(['author', 'tags'])
                    ->withCount('likes')
                    ->withAvg('ratings', 'stars')
                    ->latest()
                    ->take(8);
            }])
            ->get();

        // 2. ตรวจสอบว่ามีการค้นหาคำ ค้นหาแท็ก หรือกรองหมวดหมู่หรือไม่
        $isSearching = $request->filled('search') || $request->filled('tag') || $request->filled('category');

        if ($isSearching) {
            $query = Novel::with(['author', 'category', 'tags'])
                ->withCount('likes')
                ->withAvg('ratings', 'stars');

            // ค้นหาด้วยคำค้น
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('username', 'LIKE', "%{$search}%");
                    });
                });
            }

            // ค้นหาด้วยแท็ก[cite: 1]
            if ($request->filled('tag')) {
                $tag = $request->get('tag');
                $query->whereHas('tags', function ($q) use ($tag) {
                    $q->where('name', $tag);
                });
            }

            // กรองตามหมวดหมู่
            if ($request->filled('category')) {
                $query->where('category_id', $request->get('category'));
            }

            $novels = $query->orderByDesc('ratings_avg_stars')
                ->orderByDesc('likes_count')
                ->orderByDesc('created_at')
                ->paginate(16)
                ->withQueryString();

            // จุดสำคัญ: ส่งทั้ง $novels และ $categories ไปด้วย
            return view('welcome', compact('novels', 'categories'));
        }

        // 3. กรณีหน้าแรกปกติ (ดึงนิยายยอดนิยม Top 8 เรื่องซ้ายสุดคืออันดับ 1)
        $popularNovels = Novel::with(['author', 'category', 'tags'])
            ->withCount('likes')
            ->withAvg('ratings', 'stars')
            ->orderByDesc('view_count')
            ->orderByDesc('ratings_avg_stars')
            ->take(8)
            ->get();

        return view('welcome', compact('popularNovels', 'categories'));
    }

        // หน้าเลือกแพ็กเกจ
    public function show() {
        return view('topup.index');
    }

    // หน้าอัปโหลดสลิป
    public function payment(Request $request) {
        $amount = $request->query('amount');
    
    // ตรวจสอบว่ามีการส่ง amount มาไหม และต้องเป็นตัวเลขที่มากกว่า 0
    if (!$amount || !is_numeric($amount) || $amount <= 0) {
        return redirect()->route('topup.index')->with('error', 'กรุณาระบุจำนวนเงินที่ถูกต้อง');
    }

    // คำนวณเหรียญใหม่ที่นี่ (ป้องกันการแก้ URL แอบเพิ่มเหรียญเอง)
    $coins = floor($amount);

        return view('topup.payment', compact('amount', 'coins'));
    }

    // บันทึกข้อมูล
    public function process(Request $request) {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'slip_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // อย่าเชื่อค่า coins_to_receive จากฟอร์ม เพราะผู้ใช้แก้ HTML ได้
        $coins = (int) floor($request->amount);

        $path = $request->file('slip_image')->store('slips', 'public');

        CoinTopup::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'coins_to_receive' => $coins,
            'slip_image' => $path,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'ส่งหลักฐานเรียบร้อย รอแอดมินตรวจสอบครับ');
    }

    public function history()
    {
        // ดึงรายการเติมเงินของ user คนนี้ เรียงจากใหม่ไปเก่า
        $topups = \App\Models\CoinTopup::where('user_id', auth()->id())
                    ->latest()
                    ->paginate(10);

        return view('topup.history', compact('topups'));
    }
}
