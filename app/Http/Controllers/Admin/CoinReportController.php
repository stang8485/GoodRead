<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChapterPurchase;
use App\Models\CoinTopup;
use App\Models\CoinTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoinReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. รับค่าสัปดาห์
        $week = $request->input('week', now()->format('Y-\WW'));

        try {
            $start = Carbon::parse($week)->startOfWeek(); // วันจันทร์ 00:00:00
        } catch (\Throwable $e) {
            $start = now()->startOfWeek();
            $week = $start->format('Y-\WW');
        }

        $end = $start->copy()->endOfWeek(); // วันอาทิตย์ 23:59:59

        // 2. คำนวณรหัสสำหรับปุ่มนำทาง
        $prevWeek = $start->copy()->subWeek()->format('Y-\WW');
        $nextWeek = $start->copy()->addWeek()->format('Y-\WW');
        $currentWeek = now()->format('Y-\WW');

        // 3. คำนวณยอดใช้เหรียญซื้อตอนโดยตรงจาก ChapterPurchase (ตรงกับตารางประวัติ 100%)
        $coinsSpent = ChapterPurchase::whereBetween('created_at', [$start, $end])
            ->sum('price_paid');

        // 4. คำนวณยอดเหรียญที่เติม (จาก CoinTopup ที่อนุมัติแล้ว)
        $coinsToppedUp = CoinTopup::where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->sum('coins_to_receive');

        // 5. คำนวณยอดเงินบาทที่เติม (จาก CoinTopup ที่อนุมัติแล้ว)
        $approvedTopupMoney = CoinTopup::where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        // 6. ดึงรายการประวัติการเติมเงิน
        $topups = CoinTopup::with(['user', 'admin'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->paginate(20, ['*'], 'topups')
            ->withQueryString();

        // 7. ดึงรายการประวัติการซื้อตอน
        $purchases = ChapterPurchase::with(['user', 'chapter.novel'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->paginate(20, ['*'], 'purchases')
            ->withQueryString();

        return view('admin.coin-reports.index', compact(
            'week',
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