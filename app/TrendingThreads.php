<?php

namespace App;
Use Illuminate\Support\Facades\Redis;
Use App\PopularThread;

class TrendingThreads 
{
    public $production_key = 'popular_threads';
    public $testing_key = 'testing_popular_threads';

    public function getTrendingThreads()
    {
        /*
            if(app()->environment() === 'testing')
                return array_map('json_decode',Redis::zrevrange($this->testing_key,0,5));

            return array_map('json_decode',Redis::zrevrange($this->production_key,0,5));
        */
        return PopularThread::orderBy('visit_count','desc')->take(5)->get();
    }

    public function increment_visit_count($thread)
    {
        /*
            if(app()->environment() === 'testing')
                Redis::zincrby($this->testing_key,1,json_encode([ 'id'=>$thread->id, 'title'=>$thread->title, 'path'=>$thread->getPathAttribute() ]));
            else
                Redis::zincrby($this->production_key,1,json_encode([ 'id'=>$thread->id, 'title'=>$thread->title, 'path'=>$thread->getPathAttribute() ]));
        */

        $record = PopularThread::firstOrCreate(['thread_id'=>$thread->id],
            ['thread_title'=>$thread->title,
             'visit_count'=>0,
             'thread_path'=>$thread->path,
            ]
        );
        
        $record->increment('visit_count');
        //PopularThreads::where('thread_id',$thread->id)->increment('total_visits');
    }
}