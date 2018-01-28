<?php

namespace Tests\Feature;

use Tests\TestCase;
use Session;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Filters\Threadfilter\ThreadFilter as ThreadSpamFilter;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;


    /** @test
    */

    function authenticated_user_can_create_thread()
    {
        //Given an authenticated user
        // $this->actingAs(factory('App\User')->create());
        $user = create('App\User',['name'=>'Yeasir']);
        $this->signIn($user);


        //and a thread

        $thread = make('App\Thread',['user_id'=>$user->id]);
        //dd('here after make');
        //$user->confirmed = true;

        //$user = auth()->user();
        $confirmation_token = str_random(15);
        $user->confirmation_token = $confirmation_token;
        $user->save();
        //dd($thread);
        $this->get(route('confirm.registered.email',['confirmation_token'=>$user->fresh()->confirmation_token]));
        //when user submits a form to create a thread
        //dd($user);

        //since the request is sent from test environment to the controller, so from controller we are getting a json representation of the thread that was persisted into db using the attributes of the $thread object that was made here(make('App\Thread'))
        $created_thread = $this->post(route('thread.store'),$thread->toArray());
        //dd($created_thread);
           
        
        
        //dd(route('threads.show',['channel'=>$created_thread->original->channel->slug,'thread'=>$created_thread->original->id]));
        
        
        //the thread can be seen
        $this->get(route('threads.show',['channel'=>$created_thread->original->channel->slug,'thread'=>$created_thread->original->id]))
            ->assertSee($thread->body);

    }

    /** @test
    */
    function unauthenticated_user_cannot_create_a_thread()
    {
    
        // $thread = factory('App\Thread')->make();
        $thread = make('App\Thread');
        
        $this->expectException('Illuminate\Auth\AuthenticationException');

        $this->post(route('thread.store'),$thread->toArray());

        //the thread can be seen
        // $this->get(route('threads.show',$thread->id))
        //     ->assertSee($thread->body);
    }

    /** @test
    */
    public function users_must_confirm_email_address_before_they_can_create_a_request()
    {
        $this->signIn(create('App\User',['name'=>'Mohi']));
        $thread = make('App\Thread',[
            'user_id'=>auth()->user()->id,
        ]);

        $this->withExceptionHandling()
            ->post(route('thread.store'),$thread->toArray())
            ->assertStatus(403);

        $user = auth()->user();
        $user->confirmed = true;
        $user->save();


        $this->withExceptionHandling()
            ->post(route('thread.store'),$thread->toArray())
            ->assertStatus(200);
    }

    /** @test
    */    
    function unauthenticated_user_cannot_view_create_thread_page()
    {
        //$this->expectException('Illuminate\Auth\AuthenticationException');
        $this->withExceptionHandling()->get(route('thread.create'))
            ->assertRedirect('/login');
    }


    /** @test
    */

    public function unauthorized_user_cannot_delete_a_thread()
    {
        //->withExceptionHandling()
            
        $thread = create('App\Thread');

        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->delete(route('thread.delete',['channel'=>$thread->channel,'thread'=>$thread]));
            //->assertRedirect('login');

        $this->signIn();
        $this->delete(route('thread.delete',['channel'=>$thread->channel,'thread'=>$thread]))
            ->assertStatus(403);
    }

    /** @test
    */

    public function authenticated_user_can_delete_a_thread()
    {
        $this->signIn();
        $thread = create('App\Thread',['user_id'=>auth()->id()]);
        $reply = create('App\Reply',['thread_id'=>$thread->id]);
        //dd($this->deleteJson(route('thread.delete',['channel'=>$thread->channel,'thread'=>$thread])));
        //dd($this->delete(route('thread.delete',['channel'=>$thread->channel,'thread'=>$thread]))->getContent());
        $this->delete(route('thread.delete',['channel'=>$thread->channel,'thread'=>$thread]));
        $this->assertDatabaseMissing('threads',['id'=>$thread->id]);
        $this->assertDatabaseMissing('replies',['id'=>$reply->id]);
    }

    
    /** @test
    */
    function a_thread_belongs_to_a_channel()
    {
        $thread = make('App\Thread');
        //dd($thread);
        $this->assertInstanceOf('App\Channel', $thread->channel);
    }


    public function simulateConfirmEmail($user)
    {
        $confirmation_token = str_random(15);
        $user->confirmation_token = $confirmation_token;
        $user->save();
        $this->get(route('confirm.registered.email',['confirmation_token'=>$user->fresh()->confirmation_token]));
        return $this;
    }

    /** @test
    */

    function a_thread_requires_a_title()
    {
        $this->withExceptionHandling()
            ->signIn()
            ->simulateConfirmEmail(auth()->user())
            ->createThread(['title'=>null])
            ->assertSessionHasErrors('title');
    }

    /** @test
    */
    function a_thread_requires_a_body()
    {
        $this->withExceptionHandling()
            ->signIn()
            ->simulateConfirmEmail(auth()->user())
            ->createThread(['body'=>null])
            ->assertSessionHasErrors('body');
    }

    /** @test
    */
    function a_thread_requires_a_valid_channel()
    {
        $this->withExceptionHandling()
            ->signIn()
            ->simulateConfirmEmail(auth()->user())
            ->createThread()
            ->assertSessionHasErrors('channel_id');
    }

    function createThread($attr = [])
    {
       
        //dd('inside createThread');
        //$thread = make('App\Thread');
        $thread = [
            'user_id'=>auth()->user()->id,
            'channel_id'=>null,
            'title'=>'hi there',
            'body'=>'hi there body',
        ];

        $thread = array_merge($thread, $attr);
       
        return $this->post(route('thread.store'),$thread);

        // return $this;
    }

    /** @test
    */
    public function a_user_cannot_create_spam_thread()
    {
        $this->signIn();
        $thread1 = make('App\Thread',[
            'title'=>'my sexy thread',
            'body'=> 'goooood body',
        ]);

        $this->expectException('Exception');
        $this->post(route('thread.store'),$thread1->toArray());
    }

    /** @test
    */
    public function invalid_token_requests_for_mail_confirmation_are_redirected_to_threads_page()
    {
        $user = create('App\User');
        $this->signIn($user);

        $confirmation_token = str_random(15);
        $user->confirmation_token = $confirmation_token;
        $user->save();

        $this->get(route('confirm.registered.email'),['confirmation_token'=>'habijabi'])
            ->assertStatus(302);
    }
            
}