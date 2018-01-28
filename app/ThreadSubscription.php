<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class ThreadSubscription extends Model
{
    //
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }
}
