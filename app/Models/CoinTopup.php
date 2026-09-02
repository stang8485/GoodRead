<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinTopup extends Model {
    protected $fillable = [
        'user_id',
        'amount',
        'coins_to_receive', 
        'slip_image',
        'status',
        'approved_by'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    // แอดมินที่เป็นคนอนุมัติ
    public function admin() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function transaction() {
        return $this->hasOne(CoinTransaction::class);
    }
}
