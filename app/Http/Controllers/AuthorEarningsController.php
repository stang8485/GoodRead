<?php

namespace App\Http\Controllers;

use App\Models\ChapterPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorEarningsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->subDays(29)->toDateString());
        $to = $request->input('to', now()->toDateString());
        $authorId = $request->user()->id;

        $earnings = ChapterPurchase::query()
            ->join('chapters', 'chapter_purchases.chapter_id', '=', 'chapters.id')
            ->join('novels', 'chapters.novel_id', '=', 'novels.id')
            ->where('novels.author_id', $authorId)
            ->whereDate('chapter_purchases.created_at', '>=', $from)
            ->whereDate('chapter_purchases.created_at', '<=', $to)
            ->selectRaw('DATE(chapter_purchases.created_at) as earned_on, novels.id as novel_id, novels.title as novel_title, SUM(chapter_purchases.price_paid) as coins')
            ->groupBy('earned_on', 'novels.id', 'novels.title')
            ->orderByDesc('earned_on')->orderBy('novels.title')->get();

        $dailyTotals = $earnings->groupBy('earned_on')->map(fn ($rows) => $rows->sum('coins'));
        return view('author.earnings.index', compact('from', 'to', 'earnings', 'dailyTotals'));
    }
}
