<?php

namespace Tests\Unit;
use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
    */

    public function a_user_can_fetch_their_latest_reply()
    {
        $this->signIn();
        $user = auth()->user();

        $reply = create('App\Reply',[
            'user_id' => auth()->user()->id,
        ]);

        $this->assertEquals($reply->id, $user->latestReply($reply->thread->id)->id);
    }

    /** @test
    */
    public function it_can_determine_if_a_user_can_modify_a_profile()
    {
        $user1 = create('App\User',['name'=>'johnDoe']);
        $this->withExceptionHandling()->signIn($user1);

        $user2 = create('App\User',['name'=>'janeDoe']);
        

        $this->assertTrue(Gate::allows('update-profile',$user1));
        //dd('here');
        $this->assertFalse(Gate::allows('update-profile',$user2));
    }
}