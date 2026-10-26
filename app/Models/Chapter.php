<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chapter extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'novel_id', 
        'title', 
        'content', 
        'chapter_number', 
        'price', 
        'is_free', 
        'status', 
        'scheduled_at',
        'view_count'
    ];

    // เชื่อมตัวนิยาย
    public function novel()
    {
        return $this->belongsTo(Novel::class);
    }
    public function purchases() {
        return $this->hasMany(ChapterPurchase::class);
    }
    // คอมเมนต์
    public function comments() 
    {
        return $this->hasMany(Comment::class)->latest();
    }
}