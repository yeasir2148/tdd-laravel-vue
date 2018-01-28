<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

//use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;
    /** @test
     * A basic test example.
     *
     * @return void
     */
    public function a_reply_is_owned_by_a_user()
    {
        
        //Given
        $reply = factory('App\Reply')->create();

        //Then

        $this->assertInstanceOf('App\User', $reply->owner);
    }

    /** @test
    */
    public function a_reply_belongs_to_a_thread()
    {
        $reply = create('App\Reply');
        $this->assertEquals($reply->thread_id, $reply->thread->id);
    }

    /** @test
    */
    public function a_reply_knows_if_it_was_created_within_a_specified_interval()
    {
        $reply = create('App\Reply');

        $this->assertFalse($reply->wasCreatedAgo(1));

        sleep(61);
    
        $this->assertTrue($reply->wasCreatedAgo(1));        
    }

    /** @test
    */
    public function when_a_repy_has_mentions_it_is_embedded_into_a_link_before_it_is_saved()
    {
        $thread = create('App\Thread');
        $user = create('App\User',[
            'name' => 'johnDoe',
        ]);
        $reply = [
            'body' => 'Hi Bro @johnDoe how you doin\'',
            'user_id' => 1,
        ];

        $thread->addReply($reply);

        $this->assertDatabaseHas('replies',[
            'body' => 'Hi Bro <a href="'.route('user.profile',$user->id).'">'.$user->name.'</a> how you doin\''
        ]);

    }
}
