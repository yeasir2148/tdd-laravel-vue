<?php

namespace Tests\Feature;

use Tests\TestCase;
//use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubscribeToThreadsTest extends TestCase
{
    
    use DatabaseMigrations;

    protected $thread;

    function setUp()
    {
        parent::setUp();
        $this->thread = create('App\Thread');

    }

    /** @test
    */
    public function authenticated_user_can_subscribe_to_a_thread()
    {
        $this->signIn();
        //$this->withExceptionHandling();
        $this->post(route('thread.subscribe',['channel'=>$this->thread->channel->slug,'thread'=>$this->thread->id]),$this->thread->toArray());

        $this->assertCount(1, $this->thread->subscriptions);        //because the subscription relation is not eager loaded, so this works. if the relation was eager loaded, then we would have to use $this->thread->fresh()->subscriptions to make it work.

        
    }
    
}
