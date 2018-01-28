<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Thread;
use App\Activity;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_location', 'confirmed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function threads()
    {
        return $this->hasMany('App\Thread');
    }

    public function activities()
    {
        return $this->hasMany('App\Activity');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\ThreadSubscriptions');
    }

    public function latestReply($thread_id)
    {
        return $this->hasOne('App\Reply')->where('thread_id', $thread_id)->latest()->first();
    }

    public function avatarPath()
    {
        return $this->avatar_location;
    }
}
