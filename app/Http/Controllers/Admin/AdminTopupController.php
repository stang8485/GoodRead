<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Models\CoinTopup;
use App\Models\CoinTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminTopupController extends Controller
{
    public function index() 
    {
        $topup = CoinTopup::all();
        return view('admin.topup.index', compact('topup'));
    }

    public function approve($id)
    {
        // ตรวจสอบสิทธิ์ 
        if (auth()->user()->role_id > 2) { 
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้');
        }

        $topup = CoinTopup::findOrFail($id);

        // ป้องกันการกดอนุมัติซ้ำ
        if ($topup->status !== 'pending') {
            return back()->with('error', 'รายการนี้ถูกดำเนินการไปแล้ว');
        }

        DB::transaction(function () use ($topup) {
            $topup = CoinTopup::lockForUpdate()->findOrFail($topup->id);
            if ($topup->status !== 'pending') {
                throw new \RuntimeException('รายการนี้ถูกดำเนินการไปแล้ว');
            }
            // อัปเดตสถานะในตาราง coin_topup
            $topup->update([
                'status' => 'approved',
                'approved_by' => auth()->id()
            ]);

            // เพิ่มเหรียญให้ User
            $user = User::lockForUpdate()->findOrFail($topup->user_id);
            $user->increment('coin_balance', $topup->coins_to_receive);
            $user->refresh();

            CoinTransaction::create([
                'user_id' => $user->id,
                'type' => 'topup',
                'coins_change' => $topup->coins_to_receive,
                'balance_after' => $user->coin_balance,
                'coin_topup_id' => $topup->id,
            ]);
            
        });

        return back()->with('success', 'เติมเหรียญให้ผู้ใช้เรียบร้อยแล้ว');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $topup = CoinTopup::findOrFail($id);

        if ($topup->status !== 'pending') {
            return back()->with('error', 'รายการนี้ถูกดำเนินการไปแล้ว');
        }

        DB::transaction(function () use ($topup, $request) {
            // อัปเดตสถานะเป็น rejected
            $topup->update([
                'status' => 'rejected',
                'approved_by' => auth()->id()
            ]);

        });

        return back()->with('success', 'ปฏิเสธรายการเรียบร้อยแล้ว');
    }
}
