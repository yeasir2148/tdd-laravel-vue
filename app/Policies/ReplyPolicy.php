<?php

namespace App\Policies;

use App\User;
use App\Reply;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Reply $reply)
    {
        //dd($user->id == $reply->user_id);
        return $user->id == $reply->user_id;
    }


    /*here $parameters array will have it's first element removed from what was originally passed to it from the controller method, IF the first element was the name of the model class to which this policy belongs. So this array now has only the custom parameters that was passed to it. in our case, these are 1. the thread id to which the user wants to post the reply to and 2. the minimum interval for posting reply by the same user to the same thread*/

    public function create(User $user, ...$parameters)      
    {
        $thread_id = $parameters[0];
        //$interval = $parameters[1];
        $interval = $parameters[1];
        //dd('in policy');
        //if there are no replies posted by user to this thread, then return true immediately cause he is allowed to post a reply
        if( ! $user->latestReply($thread_id) )
        {
            return true;

        }
        
        //else, there is at least one reply that the user posted to this thread, so check if it satisfies the minimum interval condition for posting a reply again
        return $user->latestReply($thread_id)->wasCreatedAgo($interval);
    }
}
