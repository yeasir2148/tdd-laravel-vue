<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Channel;
use App\Filters\ThreadFilter;
use App\Filters\Threadfilter\ThreadFilter as ThreadSpamFilter;
use \Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\TrendingThreads;
use App\Reply;

class ThreadsController extends Controller
{
    
    protected $query_string_array = [];

    public function __construct()
    {
        $this->middleware('auth')->except(['index','show','getPaginatedReplies']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($channel_slug = null, ThreadFilter $threadFilter, TrendingThreads $trending_threads)
    {
        //
        $this->query_string_array = request()->query();
        //dd($this->query_string_array);

        if($channel_slug)
        {
            $channel = Channel::whereSlug($channel_slug)->first();
            $threads = $channel->threads()->with(['replies.owner','creator','replies.favorites']);
        }
        else
        {
            $threads = Thread::with(['channel','replies.owner','creator'])->latest();
            
        }

        
        if(!empty($this->query_string_array))
        {
            //dd($this->query_string_array);
            $filters = request()->query();
            //dd($filters);
            $threads = $threads->filter($filters, $threadFilter);

            
        }

        $threads = $threads->paginate(10);
        
        if(!empty($this->query_string_array) && request()->wantsJson())
        {
            if(isset($this->query_string_array['popularity']))

            {    
                $sorted_thread_reply_count = [];
                
                foreach($threads as $thread)
                {
                    array_push($sorted_thread_reply_count, $thread->replies_count);
                }
                
                return json_encode($sorted_thread_reply_count);
            }

            if(isset($this->query_string_array['repliesCount']))
            {
                return json_encode($threads);
            }
        }

        $popular_threads = $trending_threads->getTrendingThreads();
        //$popular_threads = array_map('json_decode',Redis::zrevrange('popular_threads',0,5));
        //dd($popular_threads);
        
        return view('threads.index',compact('threads','popular_threads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('threads.create-thread');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        //dd($request->all());

        //################## Filter spam/banned words and repeated characters ########################
        // it throws up an exception if spam is detected.
        // try{
        //     $threadSpamFilter->applyFilters(
        //             ['thread_title'=>$request->title, 'thread_body'=>$request->body]
        //         );

        // }catch(Exception $exception){
        //     if($request->ajax())
        //         return response()->json(['errorMessage'=>$exception->getMessage()],500);
        //     else
        //         return response('Sorry, ' . $exception->getMessage(),500);
        // }
        //############################################################################################


        //dd('inside controller method');

        $this->validate($request, [
            'title'=>'required | max:100 | spamFree:thread',
            'body'=>'required | spamFree:thread',
            'channel_id'=>'required | numeric | exists:channels,id',
        ]);

        //dd('inside controller method');

        $thread = Thread::create([
            'user_id'=>auth()->id(),
            'title'=>$request->title,
            'body'=>$request->body,
            'channel_id'=>$request->channel_id,
        ]);

        if(app()->environment() === 'testing')
        {
            //dd('hi');
            return $thread;
            //return response()->json($thread);   //if request is coming from phpunit/test environment then send back the creted thread object as part of json response
            //return $thread->toArray();
        }
        
        else 
            return redirect()->route('threads.show',['channel'=>$thread->channel->slug,'thread'=>$thread->id])->with('flash','Thread created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channel_slug, Thread $thread, TrendingThreads $trending_threads)
    {
        if(auth()->check())
        {
            $thread->setHasUpdated_cache_key(auth()->user());
        }

        $trending_threads->increment_visit_count($thread);
        //$thread->register_visit();

        $replies = $thread->replies()->paginate(1);
        return view('threads.show',compact('thread','replies'));
    }


    public function getPaginatedReplies(Thread $thread)
    {
        $replies_wtih_pagination = $thread->replies()->paginate(3);
        return response()->json($replies_wtih_pagination);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \Ap\Channel  $channel
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update',$thread);

        if($thread->user_id != auth()->id())
        {
            if(app()->runningUnitTests())
            {
                return response()->json(['status'=>'You are not authorized to delete a thread'],403);
            }

            return redirect('login');
        }

        $deleted = $thread->delete();
        //dd($deleted);
        //dd($thread);
        //return redirect()->route('threads.all');
        //$threads= Thread::paginate(10);
        if(app()->runningUnitTests())
        {
            return response("", 204);
        }

        return redirect()->route('threads.all');
    }

    public function addBestReply(Thread $thread, Reply $reply)
    {
        $this->authorize('update',$thread);
        $returns = $thread->mark_best_reply($reply);
        //dd($returns);
        if(request()->ajax())
        {
            return $returns;
        }
    }
}
