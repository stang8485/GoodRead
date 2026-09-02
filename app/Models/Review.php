<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'novel_id',
        'rating',
        'content',
        'review_tags',
        'is_anonymous',
    ];

    // แปลง review_tags จาก JSON เป็น Array ให้อัตโนมัติเวลาดึงมาใช้งาน
    protected $casts = [
        'review_tags' => 'array',
        'is_anonymous' => 'boolean',
    ];

    // ความสัมพันธ์: รีวิวนี้เป็นของ User คนไหน
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ความสัมพันธ์: รีวิวนี้เป็นของนิยายเรื่องไหน
    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }
}