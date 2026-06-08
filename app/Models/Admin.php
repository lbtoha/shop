<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'admin_role_id',
        'is_supper_admin',
        'password',
    ];

    protected $appends = [
        'full_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getSearchAttribute()
    {
        return [
            'first_name',
            'last_name',
            'email',
            'phone',
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function role()
    {
        return $this->belongsTo(AdminRole::class, 'admin_role_id');
    }

    public function hasCap($cap, $and = true)
    {
        if (empty($this->admin_role_id)) {
            return true;
        } // It is super admin :)

        if (! isset($this->role)) {
            return false;
        }
        if (! is_array($this->role->caps)) {
            return false;
        }

        if (is_array($cap)) {
            if ($and) {
                $intersection = array_intersect($this->role->caps, $cap);
                if (count($intersection) == count($cap)) {
                    return true;
                }
            } else {
                return (bool) array_intersect($this->role->caps, $cap);
            }
        } else {

            if (in_array($cap, $this->role->caps)) {
                return true;
            }
        }

        return false;
    }
}
