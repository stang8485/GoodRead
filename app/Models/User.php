<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];
    protected $fillable = [
    'username',
    'first_name',
    'last_name',
    'email',
    'password',
    'phone_number',
    'role_id',
    'coin_balance',
    ];

    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // public function purchases()
    // {
    //     return $this->hasMany(ChapterPurchase::class);
    // }

    public function purchases()
    {
        return $this->hasMany(ChapterPurchase::class, 'user_id');
    }
    
    public function hasPurchased($chapterId) {
        return $this->purchases()->where('chapter_id', $chapterId)->exists();
    }

    public function coinTransactions()
    {
        return $this->hasMany(CoinTransaction::class);
    }

    public function novels()
    {
        return $this->hasMany(Novel::class, 'author_id');
    }
    // ชั้นหนังสือ
    public function bookmarkedNovels()
    {
        return $this->belongsToMany(Novel::class, 'bookmarks', 'user_id', 'novel_id')
                    ->withPivot('last_chapter_id')
                    ->withTimestamps();
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
}
