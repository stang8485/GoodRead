<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterPurchase extends Model
{
    protected $fillable = [
        'user_id', 
        'chapter_id', 
        'price_paid' 
    ];

    // เชื่อมโยงกับผู้ซื้อ
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // เชื่อมโยงกับตอนที่ซื้อ
    public function chapter()
    {
        return $this->belongsTo(Chapter::class)->withTrashed();
    }

    public function transaction()
    {
        return $this->hasOne(CoinTransaction::class);
    }
}
