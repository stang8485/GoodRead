<?php

namespace App\Http\Controllers;

use App\Models\ChapterPurchase;
use App\Models\Novel; 
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class AuthorEarningsController extends Controller
{
    public function index(Request $request)
    {
        $authorId = $request->user()->id;

        // รับค่าจาก Request
        $date = $request->input('date');
        $month = $request->input('month');
        $novelId = $request->input('novel_id');
        
        // กำหนดค่าเริ่มต้นของช่วงเวลา (30 วันย้อนหลัง)
        $from = $request->input('from', now()->subDays(29)->toDateString());
        $to = $request->input('to', now()->toDateString());

        //  ดึงรายชื่อนิยายของนักเขียนคนนี้ทั้งหมด สำหรับใช้ทำ Dropdown ในหน้า View
        $novels = Novel::where('author_id', $authorId)->select('id', 'title')->get();

        //  สร้าง Query
        $query = ChapterPurchase::query()
            ->join('chapters', 'chapter_purchases.chapter_id', '=', 'chapters.id')
            ->join('novels', 'chapters.novel_id', '=', 'novels.id')
            ->where('novels.author_id', $authorId); // บังคับสิทธิ์ให้ดูได้แค่ของตัวเองเท่านั้น

        //  เงื่อนไขกรองตามชื่อนิยาย
        if ($novelId) {
            $query->where('novels.id', $novelId);
        }

        //  เงื่อนไขกรองตาม วัน / เดือน / ช่วงเวลา
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

        // ดึงข้อมูลและจัดกลุ่ม
        $earnings = $query->selectRaw('DATE(chapter_purchases.created_at) as earned_on, novels.id as novel_id, novels.title as novel_title, chapter_purchases.price_paid as coins, chapter_purchases.created_at')
            ->orderByDesc('chapter_purchases.created_at')
            ->orderBy('novels.title')
            ->get();

        $dailyTotals = $earnings->groupBy('earned_on')->map(fn ($rows) => $rows->sum('coins'));

        if ($request->export === 'pdf') {
            $fontPath = storage_path('fonts');
            if (!File::exists($fontPath)) {
                File::makeDirectory($fontPath, 0777, true);
            }
            $pdf = Pdf::setOptions([
                'fontDir' => storage_path('fonts'),     // บังคับให้เก็บไฟล์ฟอนต์ที่นี่
                'fontCache' => storage_path('fonts'),   // บังคับให้เก็บแคชที่นี่
                'chroot' => public_path(),              // อนุญาตให้ระบบอ่านไฟล์ในโฟลเดอร์ public (สำคัญมากสำหรับการโหลดฟอนต์)
                'isRemoteEnabled' => true               // อนุญาตให้โหลดไฟล์ภายนอกได้ (เผื่อมีรูปภาพ)
            ])->loadView('author.earnings_pdf', compact('earnings', 'dailyTotals'));
            
            return $pdf->stream('earnings_report.pdf');
        }

        // ส่งข้อมูลไปยัง View
        return view('author.earnings.index', compact(
            'from', 'to', 'date', 'month', 'novelId', 'novels', 'earnings', 'dailyTotals'
        ));
    }
}