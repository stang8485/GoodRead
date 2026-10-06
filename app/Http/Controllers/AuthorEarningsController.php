<?php

namespace App\Http\Controllers;

use App\Models\ChapterPurchase;
use App\Models\Novel; // เพิ่ม Model Novel
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorEarningsController extends Controller
{
    public function index(Request $request)
    {
        $authorId = $request->user()->id;

        // 1. รับค่าฟิลเตอร์จาก Request
        $date = $request->input('date');
        $month = $request->input('month');
        $novelId = $request->input('novel_id');
        
        // กำหนดค่าเริ่มต้นของช่วงเวลา (30 วันย้อนหลัง)
        $from = $request->input('from', now()->subDays(29)->toDateString());
        $to = $request->input('to', now()->toDateString());

        // 2. ดึงรายชื่อนิยายของนักเขียนคนนี้ทั้งหมด สำหรับใช้ทำ Dropdown ในหน้า View
        $novels = Novel::where('author_id', $authorId)->select('id', 'title')->get();

        // 3. เริ่มต้นสร้าง Query
        $query = ChapterPurchase::query()
            ->join('chapters', 'chapter_purchases.chapter_id', '=', 'chapters.id')
            ->join('novels', 'chapters.novel_id', '=', 'novels.id')
            ->where('novels.author_id', $authorId); // บังคับสิทธิ์ให้ดูได้แค่ของตัวเองเท่านั้น

        // 4. เงื่อนไขกรองตามชื่อนิยาย
        if ($novelId) {
            $query->where('novels.id', $novelId);
        }

        // 5. เงื่อนไขกรองตาม วัน / เดือน / ช่วงเวลา
        if ($date) {
            $query->whereDate('chapter_purchases.created_at', $date);
            $from = $date;
            $to = $date;
        } elseif ($month) {
            $startOfMonth = Carbon::parse($month)->startOfMonth()->toDateString();
            $endOfMonth = Carbon::parse($month)->endOfMonth()->toDateString();
            
            $query->whereDate('chapter_purchases.created_at', '>=', $startOfMonth)
                  ->whereDate('chapter_purchases.created_at', '<=', $endOfMonth);
            $from = $startOfMonth;
            $to = $endOfMonth;
        } else {
            $query->whereDate('chapter_purchases.created_at', '>=', $from)
                  ->whereDate('chapter_purchases.created_at', '<=', $to);
        }

        // 6. ดึงข้อมูลและจัดกลุ่ม
        $earnings = $query->selectRaw('DATE(chapter_purchases.created_at) as earned_on, novels.id as novel_id, novels.title as novel_title, SUM(chapter_purchases.price_paid) as coins')
            ->groupBy('earned_on', 'novels.id', 'novels.title')
            ->orderByDesc('earned_on')
            ->orderBy('novels.title')
            ->get();

        $dailyTotals = $earnings->groupBy('earned_on')->map(fn ($rows) => $rows->sum('coins'));

        // ส่งข้อมูลไปยัง View
        return view('author.earnings.index', compact(
            'from', 'to', 'date', 'month', 'novelId', 'novels', 'earnings', 'dailyTotals'
        ));
    }
}