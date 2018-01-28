<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Redis;
use App\TrendingThreads;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
    */
    function authenticated_user_can_participate_in_a_thread()
    {
        //Given a thread
        $thread = factory('App\Thread')->create();
        
        //and an authenticated user

        $user = factory('App\User')->create();
        $this->be($user);

        //when a user replies to a thread

        $reply = factory('App\Reply')->make();
        $this->post(route('reply.store',$thread->id),$reply->toArray());

        //it will be an instance of Reply


        //or the reply in the DB has a reference to this user

        // or the reply is visible on the page

        $this->get(route('threads.show',['channel'=>$thread->channel->slug,'thread'=>$thread->id]))
                ->assertSee($reply->body);
    }

    /** @test
    */

    function unauthenticated_user_can_not_post_replies()
    {
        $thread = create('App\Thread');
        $reply = make('App\Reply',['thread_id'=>$thread->id]);
        //dd('hi');

        $this->withExceptionHandling()->post(route('reply.store',$thread->id),$reply->toArray())->assertRedirect('/login');
        //dd('hi');
    }

    /** @test
    */
    public function guest_user_cannot_delete_a_reply()
    {
        $reply = create('App\Reply');
        $this->withExceptionHandling()
            ->delete(route('reply.delete',['reply'=>$reply->id]))
            ->assertRedirect('login');
    }

    /** @test
    */
    public function unauthorised_user_cannot_delete_a_reply()
    {
        $reply = create('App\Reply');
        
        $this->signIn();
        //$this->withExceptionHandling();
        $this->expectException('Illuminate\Auth\Access\AuthorizationException');
        $this->delete(route('reply.delete',['reply'=>$reply->id]));
            //->assertStatus(403);
    }

    /** @test
    */
    public function authorized_user_can_delete_a_reply()
    {
        $this->signIn();
        $reply = create('App\Reply',[
            'user_id'=>auth()->id(),
        ]);
        
        

        $this->delete(route('reply.delete',['reply'=>$reply->id]))
            ->assertStatus(302);
        
        $this->assertDatabaseMissing('replies',['id'=>$reply->id]);

    }

    /** @test
    */
    public function a_user_cannot_post_replies_within_a_minute_of_their_last_reply()
    {
        $this->signIn();

        //$this->withExceptionHandling();

        $thread1 = create('App\Thread');
        $reply1 = make('App\Reply',[
            'user_id'=>auth()->id(),
        ]);

        $reply2 = make('App\Reply',[
            'user_id'=>auth()->id(),
            'thread_id'=>$reply1->thread_id,
        ]);

        $reply3 = make('App\Reply',[
            'user_id'=>auth()->id(),
            'thread_id'=>$reply1->thread_id,
        ]);

        $this->post(route('reply.store',$thread1->id), $reply1->toArray());
        

        sleep(61);
        
        $response = $this->postJson(route('reply.store',$thread1->id), $reply3->toArray())->json();
        
        $this->assertDatabaseHas('replies',['id' => $response['id']]);


        $this->expectException('App\Exceptions\ThrottleException');
        $this->post(route('reply.store',$thread1->id), $reply2->toArray());
    }

    /** @test
    */
    public function authenticated_users_can_fetch_all_users()
    {
        $this->signIn();
        $users =  factory('App\User',3)->create();

        $response = $this->getJson(route('users.all'))->json();
        //dd(json_decode($response->getContent()));
        $this->assertEquals(4, sizeOf($response));
    }

    /** @test
    */
    public function threads_can_be_sorted_by_popularity()
    {
        $trending_threads = New TrendingThreads;
        
        //Redis::del($trending_threads->testing_key);

        $thread1 = create('App\Thread');
        $thread2 = create('App\Thread');

        $this->get(route('threads.show',['channel' => $thread1->channel->slug,'thread' => $thread1]));
        $this->get(route('threads.show',['channel' => $thread1->channel->slug,'thread' => $thread1]));
        $this->get(route('threads.show',['channel' => $thread1->channel->slug,'thread' => $thread1]));
        $this->get(route('threads.show',['channel' => $thread1->channel->slug,'thread' => $thread1]));


        $this->get(route('threads.show',['channel' => $thread2->channel->slug,'thread' => $thread2]));
        $this->get(route('threads.show',['channel' => $thread2->channel->slug,'thread' => $thread2]));

        $response = $trending_threads->getTrendingThreads();

        //dd($response);

        $this->assertEquals($thread1->thread_title,$response[0]->title);

    }
    
}