<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    // ดึงนิยายทั้งหมดที่มีแท็กนี้
    public function novels()
    {
        return $this->belongsToMany(Novel::class, 'novel_tag', 'tag_id', 'novel_id');
    }
}
