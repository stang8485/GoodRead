<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChapterPurchase;
use App\Models\CoinTopup;
use App\Models\CoinTransaction;
use App\Models\Novel; // ต้องเพิ่ม Model Novel สำหรับดึงรายชื่อนิยาย
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoinReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. รับค่าจากฟอร์ม (วัน, เดือน, สัปดาห์, ไอดีนิยาย)
        $date = $request->input('date');
        $month = $request->input('month');
        $week = $request->input('week');
        $novelId = $request->input('novel_id');

        // 2. ตรวจสอบเงื่อนไขการหาวันเริ่มต้นและสิ้นสุด
        if ($date) {
            // ค้นหาแบบรายวัน
            $start = Carbon::parse($date)->startOfDay();
            $end = Carbon::parse($date)->endOfDay();
        } elseif ($month) {
            // ค้นหาแบบรายเดือน (YYYY-MM)
            $start = Carbon::parse($month)->startOfMonth();
            $end = Carbon::parse($month)->endOfMonth();
        } else {
            // ค้นหาแบบรายสัปดาห์ (ค่าเริ่มต้นเดิม)
            $week = $week ?: now()->format('Y-\WW');
            try {
                $start = Carbon::parse($week)->startOfWeek();
            } catch (\Throwable $e) {
                $start = now()->startOfWeek();
                $week = $start->format('Y-\WW');
            }
            $end = $start->copy()->endOfWeek();
        }

        // 3. คำนวณรหัสสำหรับปุ่มนำทาง (ยังคงไว้เผื่อใช้)
        $prevWeek = $start->copy()->subWeek()->format('Y-\WW');
        $nextWeek = $start->copy()->addWeek()->format('Y-\WW');
        $currentWeek = now()->format('Y-\WW');

        // 4. ดึงรายชื่อนิยายทั้งหมดสำหรับ Dropdown หน้าแอดมิน
        $novels = Novel::select('id', 'title')->get();

        // 5. สร้าง Query สำหรับประวัติการซื้อตอน (ใช้สำหรับการซื้อตอนและกรองตามนิยาย)
        $purchaseQuery = ChapterPurchase::with(['user', 'chapter.novel'])
            ->whereBetween('created_at', [$start, $end]);

        // ถ้ามีการเลือกนิยาย ให้กรองยอดเฉพาะนิยายเรื่องนั้น
        if ($novelId) {
            $purchaseQuery->whereHas('chapter', function ($query) use ($novelId) {
                $query->where('novel_id', $novelId);
            });
        }

        $coinsSpent = (clone $purchaseQuery)->sum('price_paid');
        $purchases = (clone $purchaseQuery)->latest()
            ->paginate(20, ['*'], 'purchases')
            ->withQueryString();

        // 6. สร้าง Query สำหรับประวัติการเติมเงิน
        // (การเติมเงินไม่ได้ผูกกับนิยาย จึงใช้แค่การกรองวันที่)
        $topupQuery = CoinTopup::whereBetween('created_at', [$start, $end]);

        $coinsToppedUp = (clone $topupQuery)->where('status', 'approved')->sum('coins_to_receive');
        $approvedTopupMoney = (clone $topupQuery)->where('status', 'approved')->sum('amount');
        
        $topups = CoinTopup::with(['user', 'admin'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->paginate(20, ['*'], 'topups')
            ->withQueryString();

        // 7. ส่งตัวแปรทั้งหมดไปที่ View
        return view('admin.coin-reports.index', compact(
            'date',
            'month',
            'week',
            'novelId',
            'novels',
            'prevWeek',
            'nextWeek',
            'currentWeek',
            'start',
            'end',
            'coinsSpent',
            'coinsToppedUp',
            'approvedTopupMoney',
            'topups',
            'purchases'
        ));
    }
}