<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Novel extends Model
{
    use HasFactory;

    // กำหนดฟิลด์ที่อนุญาตให้บันทึกข้อมูลได้
    protected $fillable = [
    'title',
    'slug',
    'blurb',
    'content_rating',
    'description',
    'category_id',
    'author_id',
    'cover_image',
    'status',
];

    // Laravel ว่าถ้าเจอ Route ใช้ slug ในการหาเสมอ
    public function getRouteKeyName()
    {
        return 'slug'; 
    }

    /**
     * ความสัมพันธ์: นิยายเรื่องนี้เป็นของใคร (ผู้แต่ง)
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * ความสัมพันธ์: นิยายเรื่องนี้อยู่ในหมวดหมู่ไหน
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * ความสัมพันธ์: นิยายเรื่องนี้มีกี่ตอน 
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * ความสัมพันธ์: นิยายเรื่องนี้มีคนกดไลก์เท่าไหร่
     */
    public function likes() {
    return $this->hasMany(Like::class);
    }
    // rate นิยาย
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // หาคะแนนเฉลี่ย
    public function averageRating()
    {
        return round($this->ratings()->avg('stars'), 1) ?: 0;
    }
    // คอมเมนต์
    public function comments() 
    {
        return $this->hasMany(Comment::class)->latest();
    }
    // เช็คว่า User คนนี้กดไลก์ไปหรือยัง
    public function isLikedBy($user) {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    // ดึง User ทั้งหมดที่กดติดตามนิยายเรื่องนี้(เอายอดไปโชว์หน้าเว็บ)
    public function followers()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'novel_id', 'user_id');
    }
    // ดึงแท็กทั้งหมดของนิยายเรื่องนี้
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'novel_tag', 'novel_id', 'tag_id');
    }
    // รีวิว
    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }
}
