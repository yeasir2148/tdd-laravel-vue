<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoriteTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
    */

    public function unauthenticated_user_cannot_favorite_a_reply()
    {
        $reply = create('App\Reply');

        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->post(route('reply.favorite',['id'=>$reply->id]));

        //$reply->addAsFavorite();
    }

    /** @test
    */
    public function authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();

        $reply = create('App\Reply');
        //dd($reply);

        $this->post(route('reply.favorite',['id'=>$reply->id]));
        //dd($reply->favorites()->count());
        $this->assertCount(1, $reply->favorites);
    }

    /** @test
    */
    public function a_user_cannot_favorite_the_same_reply_more_than_once()
    {
        $this->signIn();
        $reply = create('App\Reply');

        
        try {
            $this->post(route('reply.favorite',['id'=>$reply->id]));
            $this->post(route('reply.favorite',['id'=>$reply->id]));
        } catch (\Illuminate\Database\QueryException $e) {
            $this->fail('cannot like a reply more than once');   
        }
        $this->assertCount(1, $reply->favorites);
    }


    /** @test
    */
    public function authorized_user_can_unfavorite_a_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');
        $reply->addAsFavorite();

        $this->assertCount(1,$reply->favorites);

        $this->delete(route('favorite.delete',['favorite_id'=>$reply->favorites->first()->id]));
        $this->assertEquals(0,$reply->fresh()->favorites->count());
    }
}