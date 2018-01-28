<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Favorite;
use App\Activity;
use App\Traits\RecordsActivity;
use Auth;
use Carbon\Carbon;


class Reply extends Model
{
    //
    use RecordsActivity;

    protected static function boot()
    {
        parent::boot();

        static::created(function($reply){
            //echo('created reply for thread: '.$reply->thread->title);
            $reply->thread->touch();
        });

        // static::deleted(function($reply){
        //     if($reply->is_best_reply)
        //     {
        //         //dd('here');
        //         $reply->thread->update(['best_reply'=>null]);
        //     }
        // });
    }

    protected $fillable = ['body','user_id','thread_id','thread_owner'];
    protected $with = ['owner','favorites',];
    protected $appends = ['isFavoritedByCurrentUser','favorites_count','can_update','is_best_reply'];
    //protected $touches = ['thread'];

    //protected $morphClass = 'Reply';

    public function owner()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function favorites()
    {
        return $this->morphMany('App\Favorite','favorited');
    }

    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'object');
    }

    // public function getIsBestReplyAttribute()
    // {
    //     return $this->id == $this->thread->best_reply;
    // }

    public function path()
    {
        return route('threads.show',['channel'=>$this->thread->channel->slug,'thread'=>$this->thread->id])."#Reply_".$this->id;
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }

    public function addAsFavorite()
    {
        if(!$this->favorites()->where('user_id',auth()->id())->exists())
            $this->favorites()->create(['user_id'=> auth()->id()]);
        //dd('hi');
    }

    public function isFavorited()
    {
        return $this->favorites->count();
    }

    public function isFavoritedByCurrentUser()
    {
        if(!auth()->user())
            return true;                //return true if user is not logged in. this will add class "disabled" to the button that registers a like action for that reply
        return $this->favorites()->where('user_id',auth()->user()->id)->exists();
    }

    public function getisFavoritedByCurrentUserAttribute()
    {
        return $this->isFavoritedByCurrentUser();
    }

    public function getCanUpdateAttribute()
    {
        if(!Auth::check())
            return false;
        return ($this->user_id == auth()->user()->id );
    }

    // public function getThreadOwnerAttribute()
    // {
    //     return $this->thread->creator->id;
    // }

    public function wasCreatedAgo($interval)
    {
        return $this->created_at <= Carbon::now()->subMinutes($interval);
    }

    public function getIsBestReplyAttribute()
    {
        return $this->thread()->where('best_reply',$this->id)->exists();
    }
}