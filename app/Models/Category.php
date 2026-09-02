<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * ความสัมพันธ์: หมวดหมู่หนึ่งหมวด มีนิยายได้หลายเรื่อง
     */
    public function novels()
    {
        return $this->hasMany(Novel::class, 'category_id');
    }
}
