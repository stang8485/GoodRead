<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinTransaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'coins_change', 'balance_after',
        'coin_topup_id', 'chapter_purchase_id',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function topup() { return $this->belongsTo(CoinTopup::class, 'coin_topup_id'); }
    public function chapterPurchase() { return $this->belongsTo(ChapterPurchase::class); }
}
