<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use DB;



class NotificationsTest extends TestCase
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
    public function a_notification_is_created_when_a_subscribed_thread_gets_a_new_reply()
    {
        $this->signIn();

        $this->assertCount(0, auth()->user()->notifications);

        $this->thread->subscribe();

        $this->thread->addReply([
            'user_id'=>auth()->user()->id,
            'body'=>'my new reply for notification',
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test
    */
    public function a_user_can_clear_notifications()
    {
        $this->signIn();
        $this->thread->subscribe();
        $this->thread->addReply([
            'user_id'=>auth()->user()->id,
            'body'=>'my new reply for notification',
        ]);

        $user = auth()->user();
        $this->assertCount(1, $user->fresh()->unreadNotifications);

        $notification_id = $user->unreadNotifications()->first()->id;

        $this->delete(route('notification.delete',['user'=>auth()->user()->id,'notification'=>$notification_id]));
        $this->assertCount(0, auth()->user()->fresh()->unreadNotifications);

    }

    /** @test
    */

    public function a_user_can_fetch_his_unread_notifications()
    {
        $this->signIn();
        $this->thread->subscribe();
        $this->thread->addReply([
            'user_id'=>auth()->user()->id,
            'body'=>'my new reply for notification',
        ]);

        $response = $this->getJson(route('notifications.unread',['user'=>auth()->user()->id]))->json();
        //dd($response);
        $this->assertCount(1, $response);

    }

    /** @test
    */
    public function when_a_user_is_mentioned_in_a_reply_he_is_notified()
    {
        $user1 = create('App\User',[
            'name'=>'user1',
        ]);
        
        $user2 = create('App\User',[
            'name'=>'user2',
        ]);

        $this->signIn($user1);

        $thread1 = create('App\Thread');

        $this->postJson(route('reply.store',$thread1->id),[
            'body'=>'hey @user2 how u doing says @user1 @fatafati',
        ]);

        $this->assertDatabaseHas('notifications',['notifiable_id'=>$user2->id]);
        $this->assertCount(2, DB::table('notifications')->get());
    }
}
