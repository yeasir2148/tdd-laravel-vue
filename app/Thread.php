<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Reply;
use App\User;
use App\Channel;
use App\Activity;
use App\Notifications\ThreadWasUpdated;
use App\Filters\ThreadFilter;
use App\Traits\RecordsActivity;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use App\PopularThread;


class Thread extends Model
{
    //
    use RecordsActivity;

    //protected $fillable = ['user_id','title','body','channel_id'];
    protected $guarded = [];

    protected $appends = ['replies_count','path',];

    protected $with = ['channel','replies.owner','creator','popularity'];
    //protected $with = ['channel','replies','creator'];

    //protected $morphClass = 'Thread';

    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('replies_count', function($builder){
            $builder->withCount('replies');
        });

        static::deleting(function ($thread){
            //dd('hi');
            //dd($thread->replies);
            //$replies = $thread->replies;
            //Reply::destroy($replies);
            foreach($thread->replies as $reply)
                $reply->delete();
        });

        // static::updating(function($thread){
        //     echo('updating thread');
        // });
        
    }

    protected static function getEventsToBeRecorded()
    {
        return ['created','deleted'];
    }


    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function replies()
    {
        return $this->hasMany('App\Reply')
            ->withCount('favorites');
    }

    public function activities()
    {
        //dd('hi');
        return $this->morphMany(Activity::class,'object');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\ThreadSubscription');
    }

    public function popularity()
    {
        return $this->hasOne(PopularThread::class);
    }

    public function subscribe($user_id = null)
    {
        $this->subscriptions()->create([
            'user_id'=> $user_id ?: auth()->user()->id,
        ]);

        return $this;
    }

    public function unsubscribe($user_id = null)
    {
        $this->subscriptions()
            ->where('user_id', $user_id ?:auth()->user()->id)
            ->delete();
    }

    public function addReply($reply)
    {
        //dd($reply);

        $reply = $this->replaceMentionsInReplyBody($reply);
        

        //dd($reply);

        $new_reply = $this->replies()->create($reply);

        //notifying users
        $this->notifyUsers($new_reply);
        //print_r($new_reply->toArray());
        //dd($new_reply);
        //dd($new_reply);

        //$this->touch();

        return $new_reply->load('owner');
    }

    public function mark_best_reply($reply)
    {
        $this->update(['best_reply'=> $reply->id]);
        $this->save();
        //dd($this->toArray());
        return $this->toArray();
    }

    protected function replaceMentionsInReplyBody($reply)
    {

        $body = $reply['body'];

        $any_user_mentioned = preg_match('/@([\w]+)/',$body,$matches);
        if($any_user_mentioned)
        {
            $mentioned_name = $matches[1];
            $mentioned_user = User::where('name',$mentioned_name)->first();

            //dd($mentioned_user);

            $new_body = preg_replace('/@([\w]+)/','<a href="'.route('user.profile',$mentioned_user->id).'">$1</a>',$body);

            //dd($new_body);

            $reply['body'] = $new_body;
        }

        return $reply;
    }


    protected function notifyUsers($reply)
    {
        foreach($this->subscriptions as $subscription)
        {
            $subscription->user->notify(new ThreadWasUpdated($this, $reply));
        }

        //return;
    }

    public function setHasUpdated_cache_key($user)
    {
        Cache::forever($this->generate_HasUpdated_cache_key($user),\Carbon\Carbon::now());
    }

    public function hasUpdatedSinceUserVisited($user)
    {
        if(!auth()->check())
            return true;
       
        //print_r(Cache::get($this->generate_HasUpdated_cache_key($user)));
        // echo('Hi....#');
        // print_r($this->updated_at);
        
        return $this->updated_at > Cache::get($this->generate_HasUpdated_cache_key($user));
        
        //print_r(Cache::get($this->getHasUpdated_cache_key($user), Carbon::now()));
    }

    private function generate_HasUpdated_cache_key($user = null)
    {
        return 'visited.thread.'.$this->id.'.user.'.$user->id;
    }

    public function scopeFilter($query, $filters, $threadFilter)
    {
        
        //$threadFilter = new ThreadFilter;
        $query = $threadFilter->apply($query, $filters);
        return $query;
    }

    /* This is an example of using Accessor function. Refer to this function as replies_count from the controller...or other places*/
    public function getRepliesCountAttribute()
    {
        return $this->replies->count();
    }

    public function isCurrentUserSubscribed()
    {

        if(!auth()->check())
            return false;
        
        return $this->subscriptions()->where('user_id',auth()->user()->id)->exists();
    }

    public function getPathAttribute()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    // public function getCanBeUpdatedAttribute()
    // {
    //     return auth()->user()->id == $this->user_id;
    // }

    /*
        public function total_visits()
        {
            //return Redis::get($this->total_visits_cacheKey());
            $record = PopularThread::where('thread_id',$this->id)->first();
            return $record ? $record->visit_count : 0;

        }

        public function getTotalVisitsAttribute()
        {
            $record = PopularThread::where('thread_id',$this->id)->first();
            return $record ? $record->visit_count : 0;
        }   

    */

    public function total_visits_cacheKey()
    {
        return 'thread.'.$this->id.'.visits';
    }

    // public function register_visit()
    // {
    //     Redis::incr($this->total_visits_cacheKey());
    // }

    /*
        public function resetVisit()
        {
            Redis::del($this->total_visits_cacheKey());
        } 
    */

}
