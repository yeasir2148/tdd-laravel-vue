<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateReplyRequest;
use App\Thread;
use App\Reply;
use App\User;
use Auth;
use App\Notifications\YouWereMentioned;
use App\Events\NewReplyAddedToThread;
//use App\Filters\Replyfilter\ReplyFilter;


class RepliesController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function store(Thread $thread, CreateReplyRequest $request)
    {

        //dd($request->all());
        // $this->validate($request, [
        //     'body'=>'required | max: 500 | spamFree:reply',
        // ]);

        //################## Filter spam/banned words and repeated characters ########################
        // it throws up an exception if spam is detected.

        //$replyFilter->applyFilters($request->body);

        //############################################################################################

        //calling the policy method with multiple parameters. this can be a very cool trick if you want to pass custom  parameters to policy method. check the policy class
        // try{
        //     $this->authorize('create', [Reply::class, $thread->id, 1]);     
        // }catch(\Illuminate\Auth\Access\AuthorizationException $exception)
        // {
        //     return response('Too many replies before allowed interval',429); 
        // }


        //################### All of the above validation, authorization and spam detection are now being handled in the form request class

        preg_match_all('/@([^\s\.]+)/i',$request->body,$matches);
        $mentioned_users = $matches[1];

        $new_reply = $thread->addReply(array_merge($request->all(),['user_id'=>Auth::user()->id, 'thread_owner'=>$thread->user_id] ));
        
        event(new NewReplyAddedToThread($thread, $new_reply, $mentioned_users));

        if(request()->expectsJson())
        {
            //dd('here');
            return response()->json($new_reply);
        }

        if(app()->environment() === 'testing')
        {
            //dd('here');
            return json_encode($new_reply);
        }

        return back()->with('flash','Your reply was posted successfully');
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update',$reply);

        $reply->delete();

        if(request()->ajax())
            return response()->json('hi');

        return back();
    }

    public function update(Reply $reply, Request $request)
    {
        $this->authorize('update',$reply);

        $this->validate($request, [
            'reply_body'=>'required | max: 500 | spamFree:reply',
        ]);

        //$replyFilter->applyFilters($request->reply_body);

        $reply->update(['body'=>$request->reply_body]);
        return response()->json($reply);
    }


}
