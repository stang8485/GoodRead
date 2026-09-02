<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id',
    'novel_id', 
    'chapter_id',
    'comment_text'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
