<?php

namespace Tests\Feature;

use Tests\TestCase;
//use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RreadThreadsTest extends TestCase
{
    use DatabaseMigrations;
    protected $thread;

    function setUp()
    {
        parent::setUp();
        $thread = factory('App\Thread')->create();
        $this->thread = $thread;

    }
    
    /** @test
     * A basic test example.
     *
     * @return void
     */
    public function a_user_can_browse_threads()
    {
        //$thread = factory('App\Thread')->create();
        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);
        //$response->assertStatus(200);
    }

    /** @test
     * A basic test example.
     *
     * @return void
     */
    public function a_user_can_see_a_specific_thread()
    {
        //$thread = factory('App\Thread')->create();
        $response = $this->get(route('threads.show',['channel'=>$this->thread->channel->slug,'thread'=>$this->thread->id]));
        $response->assertSee($this->thread->title);
    }

    /** @test
     *
     *
     * @return void
     */
    public function a_user_can_filter_through_a_channel()
    {
        $channel = factory('App\Channel')->create();

        $thread1  = factory('App\Thread')->create([
                'channel_id'=>$channel->id,
            ]);
        //dd($thread1);
        $thread2  = factory('App\Thread')->create();

        $this->get(route('channel.threads',$channel->slug))
            ->assertSee($thread1->body)
            ->assertDontSee($thread2->body);



        //dd($threads);
    }

    /** @test
    */
    public function a_user_can_filter_threads_by_particular_author()
    {
        $authorUser = create('App\User',['name'=>'Yeasir Arafat']);
        $notAuthorUser = create('App\User');

        $this->signIn($authorUser);

        $threadByUser = create('App\Thread',['user_id'=>$authorUser->id]);
        $threadNotByUser = create('App\Thread');

        $this->get(route('threads.all',['author'=>$authorUser->id]))
            ->assertSee($threadByUser->title)
            ->assertDontSee($threadNotByUser->title);
        //dd('hi');
    }

    /** @test
    */

    public function a_user_can_sort_threads_by_popularity()
    {
        // Given a thread with 2 replies
        $thread1 = create('App\Thread');
        $replies1 = create('App\Reply', [
            'thread_id'=>$thread1->id], 2);
        //dd('hi');
        //dd($thread1->load('replies'));
        // and a thread with 3 replies
        $thread2 = create('App\Thread');
        $replies2 = create('App\Reply',[
            'thread_id'=>$thread2->id], 3);
        //dd('hi');
        //dd($replies2->first());

        // when user visits the endpoint

        //dd($this->get(route('threads.all',['popularity'=>1]))->json());
        $response = $this->getJson(route('threads.all',['popularity'=>1]))->json();
        //dd($response);
        $this->assertEquals([3,2,0], array_slice(array_column($response,'replies_count'),0,3));

        // the returned JSON representation of the data will be sorted according to hightest reply to lowest reply thread
    }

    /** @test
    */
    public function authenticated_user_can_fetch_paginated_replies()
    {
        //$this->signIn();

        $thread = create('App\Thread');
        $reply1 = create('App\Reply',[
            'thread_id'=>$thread->id
        ],2);

        $response = $this->getJson(route('replies.paginated',['thread'=>$thread->id]))->json();
        //dd($response);
        $this->assertEquals(2, $response['total']);
    }

    /** @test
    */

    public function a_user_can_browse_unanswered_threads()
    {
        $thread1 = create('App\Thread');
        $thread2 = create('App\Thread');
        $reply1 = create('App\Reply',[
            'thread_id'=>$thread1->id,
        ]);

        $response = $this->getJson(route('threads.all',['repliesCount'=>0]))->json();
        //dd($response);
        //route('threads.all',['replies'=>0])
        //dd($response->getOriginalContent()->getData()['threads'][0]->id);    
            //->assert ;

        //dd($response->getOriginalContent()->getData());

        $this->assertCount(3, $response['data']);
        /*Explanation: because the $thread variable coming from controller is paginated, so it's content is different then what a normal thraed variable would have in such cases. to access the normal data, we have to access the 'data' key of the returned paginated variable. Here the controller method is returning the json representation of the paginated variable since the test expectsJson. Also 3 is expected count since we have one $thread variable created in setUp() method, 1 that has a reply - $thread1, and another that is created by default when creating $reply1.*/
    }

    
}
