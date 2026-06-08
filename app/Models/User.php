<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use App\Observers\UserObserver;
use App\Traits\VerifyEmailAndPhone;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, Sluggable, VerifyEmailAndPhone;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'first_name',
        'last_name',
        'username',
        'email',
        'phone',
        'address',
        'avatar',
        'status',
        'city',
        'state',
        'country',
        'zip_code',
        'is_2fa_enabled',
        'email_verified_at',
        'phone_verified_at',
        'fcm_token',
        'password',
    ];

    protected $appends = ['full_name'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_2fa_enabled' => 'boolean',
            'status' => UserStatusEnum::class,
        ];
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name.' '.($this->last_name ?? ''),
        );
    }

    public function getSearchAttribute()
    {
        return [
            'first_name',
            'last_name',
            'email',
            'phone',
            'username',
            'created_at',

        ];
    }

    public function sluggable(): array
    {
        return [
            'username' => [
                'source' => ['first_name', 'last_name'],
                'separator' => '-',
            ],
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', UserStatusEnum::ACTIVE);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', UserStatusEnum::INACTIVE);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', UserStatusEnum::BANNED);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class, 'user_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referer_id');
    }
}
