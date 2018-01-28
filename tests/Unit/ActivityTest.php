<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Activity;

Class ActivityTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
    */
    public function when_a_thread_is_created_an_activity_is_recorded()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $this->assertDatabaseHas('activities',[
            'user_id'=>auth()->id(),
            'activity_type'=>'created',
            'object_id'=>$thread->id,
            'object_type'=>'Thread'
        ]);

        $this->assertEquals(Activity::first()->object->id, $thread->id);
    }

    /** @test
    */
    public function when_a_reply_is_created_an_activity_is_recorded()
    {
        $this->signIn();
        $reply = factory('App\Reply')->create();
        //dd($reply);
        $this->assertEquals(2, Activity::count());
        //dd('Hi from '.get_class($this));
    }

     /** @test
    */
    public function when_a_thread_is_deleted_an_activity_is_recorded()
    {
        $this->signIn();
        $reply = factory('App\Reply')->create();
        //dd($reply->thread);
        $reply->thread->delete();
        //dd('Hi from '.get_class($this));
        $this->assertEquals(3, Activity::count());
        //dd('Hi from '.get_class($this));
    }

    /** @test
    */

    public function it_fetches_a_users_activities_latest_first()
    {
        $this->signIn();
        $user = auth()->user();

        $thread1 = create('App\Thread',['user_id'=>$user->id]);
        //dd($thread1);
        //dd(\Carbon\Carbon::now()->subDays(1)->format('Y-m-d'));

        $thread2 = create('App\Thread',
            [
                'user_id'=>$user->id,
                'created_at'=>\Carbon\Carbon::now()->subDays(1),
            ]);

        $thread2->activities()->first()->update(['created_at'=>'2017-11-16']);
        //dd($thread2);

        $feed = Activity::feed($user);
        //dd($feed);
        //dd(current(array_keys($feed)));
        $this->assertEquals(current(array_keys($feed)),$thread1->created_at->format('Y-m-d'));
    }
}
