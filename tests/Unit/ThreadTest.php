<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    function setUp()
    {
        parent::setUp();
        $this->thread = factory('App\Thread')->create();

        $reply = factory('App\Reply')->create([
            'thread_id'=>$this->thread->id,
        ]);

    }
   
    /** @test
    
    */

    public function a_thred_has_replies()
    {
        /*
        Given a thread
        and a thread has replies
        */

        
        /*
        When a user vists a thread
        */
        $response = $this->get(route('threads.show',['channel'=>$this->thread->channel->slug,'thread'=>$this->thread->id]));
        

        //He can see replies 
        $response->assertSee($this->thread->replies()->first()->pluck('body')[0]);

        //or He can see total reply counts for that thread
        $this->assertCount(1, $this->thread->replies);
    }

    /** @test

    */

    public function a_thread_has_a_creator()
    {
        $this->assertEquals($this->thread->creator->name, Thread::find($this->thread->id)->creator->name);
    }

    /** @test 
    */

    public function a_thread_can_add_a_reply()
    {
        $new_thread = factory('App\Thread')->create();
        $new_reply = factory('App\Reply')->make([
            'thread_id'=>$new_thread->id,
        ]);
        //dd($new_reply->thread_id);
        $new_thread->addReply($new_reply->toArray());
        $this->assertEquals($new_thread->replies()->latest()->first()->body, $new_reply->body);
    }

    /** @test
    */

    public function a_thread_can_be_subscribed_to()
    {
        //there is a thread $thread already created in the setUp method

        $this->thread->subscribe($tmp_user_id = 1);

        $this->assertCount(1, $this->thread->subscriptions()->where('user_id',1)->get());
    }

    /** @test
    */
    public function a_thread_can_be_unsubscribed_from()
    {

        $this->thread->subscribe($tmp_user_id = 1);
        $this->thread->unsubscribe($tmp_user_id = 1);

        $this->assertEquals(0, $this->thread->subscriptions()->where('user_id',1)->count());        
    }


    /** @test
    */
    public function a_thread_notifies_all_of_its_subscribers_when_a_reply_is_added()
    {
        $this->signIn();
        $user = auth()->user();
        $reply1 = create('App\Reply',['thread_id'=>$this->thread->id,'body'=>'Foo bar']);

        //dd($reply1->toArray());

        $this->thread->subscribe($user->id);

        $added_reply = $this->thread->addReply($reply1->toArray());
        
        $this->assertDatabaseHas('notifications',[
            'type'=>'App\Notifications\ThreadWasUpdated',
            'notifiable_id'=>$user->id,
            'data'=>json_encode(
                ['reply_creator'=>$reply1->owner->name,
                 'thread_title'=>str_limit($this->thread->title,10),
                 'message'=>$reply1->body,
                 'reply_link'=>$added_reply->path(),
                ])]);


    }

    /** @test
    */
    public function a_thread_can_tell_if_it_was_updated_since_a_user_last_visited_it()
    {
        //dd($this->app);

        $this->signIn();
        $user = auth()->user();

        $this->assertTrue($this->thread->hasUpdatedSinceUserVisited($user));

        $this->get(route('threads.show',['channel'=>$this->thread->channel->slug,'thread'=>$this->thread->id]));

        $this->assertFalse($this->thread->hasUpdatedSinceUserVisited($user));

        // $this->thread->addReply([
        //         'body'=>'test reply',
        //         'user_id'=>auth()->user()->id,
        //     ]);

        sleep(1);   //just to make sure that there is at least one second time difference between previous get method above and below post method. otherwise the following test fails because both the previous get and below post method occur at the same time, somethimes.

        $this->post(route('reply.store',['thread'=>$this->thread->id]),['body'=>'Reply through post']);

        //dd('done');
        $this->assertTrue($this->thread->fresh()->hasUpdatedSinceUserVisited($user));

        //dd('done');
    }

    /** @test
    */
    public function it_can_register_and_show_total_visits_to_it()
    {
        $thread1 = create('App\Thread');

        $thread1->resetVisit();

        $this->assertEquals(0, $thread1->total_visits());

        $this->get(route('threads.show',['channel'=>$thread1->channel->slug,'thread'=>$thread1->id]));

        $this->assertEquals(1, $thread1->total_visits());       
    }
}