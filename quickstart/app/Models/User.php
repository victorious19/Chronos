<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    function active_calendars() {
        return $this->hasMany(Calendar::class)->where('is_active', true)->orderBy('created_at', 'ASC')->get();
    }
    function calendars() {
        return $this->hasMany(Calendar::class)->get();
    }
    function invite($calendar_id) {
        return $this->hasMany(Invite::class)->where('calendar_id', $calendar_id)->first();
    }
    function active_invites() {
        return $this->hasMany(Invite::class)->where('status', 'active')->get();
    }
    function invites() {
        return $this->hasMany(Invite::class)->where('status', '!=', 'not_confirmed')->get();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'login',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
