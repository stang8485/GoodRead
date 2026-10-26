<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChapterPurchase;
use App\Models\CoinTopup;
use App\Models\CoinTransaction;
use App\Models\Novel; 
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class CoinReportController extends Controller
{
    public function index(Request $request)
    {
        // รับค่าจากฟอร์ม 
        $date = $request->input('date');
        $month = $request->input('month');
        $week = $request->input('week');
        $novelId = $request->input('novel_id');

        // ตรวจสอบเงื่อนไขการหาวันเริ่มต้นและสิ้นสุด
        if ($date) {
            // ค้นหาแบบรายวัน
            $start = Carbon::parse($date)->startOfDay();
            $end = Carbon::parse($date)->endOfDay();
        } elseif ($month) {
            // ค้นหาแบบรายเดือน 
            $start = Carbon::parse($month)->startOfMonth();
            $end = Carbon::parse($month)->endOfMonth();
        } else {
            // ค้นหาแบบรายสัปดาห์ 
            $week = $week ?: now()->format('Y-\WW');
            try {
                $start = Carbon::parse($week)->startOfWeek();
            } catch (\Throwable $e) {
                $start = now()->startOfWeek();
                $week = $start->format('Y-\WW');
            }
            $end = $start->copy()->endOfWeek();
        }

        // คำนวณรหัสสำหรับปุ่มนำทาง
        $prevWeek = $start->copy()->subWeek()->format('Y-\WW');
        $nextWeek = $start->copy()->addWeek()->format('Y-\WW');
        $currentWeek = now()->format('Y-\WW');

        // ดึงรายชื่อนิยายทั้งหมดสำหรับ Dropdown หน้าแอดมิน
        $novels = Novel::select('id', 'title')->withTrashed()->get();

        // สร้าง Query สำหรับประวัติการซื้อตอน 
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

        // สร้าง Query สำหรับประวัติการเติมเงิน
        $topupQuery = CoinTopup::whereBetween('created_at', [$start, $end]);

        $coinsToppedUp = (clone $topupQuery)->where('status', 'approved')->sum('coins_to_receive');
        $approvedTopupMoney = (clone $topupQuery)->where('status', 'approved')->sum('amount');
        
        $topups = CoinTopup::with(['user', 'admin'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->paginate(20, ['*'], 'topups')
            ->withQueryString();

        // ส่งตัวแปรทั้งหมดไปที่ View
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

    public function exportPdf(Request $request)
    {
        // 1. รับค่าและคำนวณช่วงเวลาเหมือนฟังก์ชัน index
        $date = $request->input('date');
        $month = $request->input('month');
        $week = $request->input('week');
        $novelId = $request->input('novel_id');

        if ($date) {
            $start = Carbon::parse($date)->startOfDay();
            $end = Carbon::parse($date)->endOfDay();
        } elseif ($month) {
            $start = Carbon::parse($month)->startOfMonth();
            $end = Carbon::parse($month)->endOfMonth();
        } else {
            $week = $week ?: now()->format('Y-\WW');
            try {
                $start = Carbon::parse($week)->startOfWeek();
            } catch (\Throwable $e) {
                $start = now()->startOfWeek();
            }
            $end = $start->copy()->endOfWeek();
        }

        // 2. ดึงข้อมูลการซื้อตอน (ใช้ get() แทน paginate())
        $purchaseQuery = ChapterPurchase::with(['user', 'chapter.novel'])
            ->whereBetween('created_at', [$start, $end]);

        if ($novelId) {
            $purchaseQuery->whereHas('chapter', function ($query) use ($novelId) {
                $query->where('novel_id', $novelId);
            });
        }

        $coinsSpent = (clone $purchaseQuery)->sum('price_paid');
        $purchases = (clone $purchaseQuery)->latest()->get(); // เปลี่ยนเป็น get()

        // 3. ดึงข้อมูลการเติมเงิน (ใช้ get() แทน paginate())
        $topupQuery = CoinTopup::whereBetween('created_at', [$start, $end]);
        
        $coinsToppedUp = (clone $topupQuery)->where('status', 'approved')->sum('coins_to_receive');
        $approvedTopupMoney = (clone $topupQuery)->where('status', 'approved')->sum('amount');
        
        $topups = CoinTopup::with(['user', 'admin'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get(); // เปลี่ยนเป็น get()

        // 4. ส่งข้อมูลไปยัง View PDF (สร้างไฟล์ resources/views/admin/coin-reports/pdf.blade.php)
        $pdf = Pdf::loadView('admin.coin-reports.pdf', compact(
            'start',
            'end',
            'coinsSpent',
            'coinsToppedUp',
            'approvedTopupMoney',
            'topups',
            'purchases'
        ));

        // 5. แสดงไฟล์ PDF
        return $pdf->stream('coin-report.pdf');
    }
}