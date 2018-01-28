<?php

namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;
    /** @test
    */
    public function authenticated_user_can_mark_a_reply_as_best()
    {
        $this->signIn();

        $thread = create('App\Thread');
        $reply = create('App\Reply',['thread_id'=>$thread->id],2);

        $this->assertNull($thread->best_reply);

        $this->post(route('thread.best-reply.store',['thread'=>$thread->id,'reply'=>$reply[1]->id] ));
        //$thread->mark_best_reply($reply[1]);

        $this->assertEquals($thread->fresh()->best_reply, $reply[1]->id);
    }

    /** @test
    */
    public function only_thread_owner_can_mark_a_best_reply()
    {
        $this->signIn()->withExceptionHandling();
        $user = create('App\User');
        $thread = create('App\Thread',['user_id'=>$user->id]);
        $reply = create('App\Reply',['thread_id'=>$thread->id],2);

        $this->post(route('thread.best-reply.store',['thread'=>$thread->id,'reply'=>$reply[1]->id] ))
            ->assertStatus(403);


    }

    /** @test
    */
    public function when_a_best_reply_is_deleted_the_corresponding_thread_is_updated_accordingly()
    {
        $user = create('App\User',['name'=>'Yeasir']);
        $this->signIn($user);
        $reply = create('App\Reply',['user_id'=>$user->id]);
        //$this->assertNull($reply->thread->best_reply);
        $reply->thread->mark_best_reply($reply);
        //dd('here');
        $this->delete(route('reply.delete',$reply->id));
        $this->assertNull($reply->thread->fresh()->best_reply);
    }

}
