<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use \Exception;
use App\Filters\Replyfilter\SpamFilter;
use App\Filters\Replyfilter\RepeatCharacterFilter;
use App\Filters\Replyfilter\ReplyFilter;

class SpamFilterTest extends TestCase
{
    
    use DatabaseMigrations;
    protected $thread, $reply;
    protected $spamFilter, $repeatCharacterFilter;
    protected $replyFilter;

    function setUp()
    {
        parent::setUp();
        $this->thread = factory('App\Thread')->create();

        $this->spamFilter = new SpamFilter;
        $this->repeatCharacterFilter = new RepeatCharacterFilter;
        $this->replyFilter = new ReplyFilter;
        //$this->reply = factory('App\Reply')->create();

    }

    /** @test
    */
    public function a_user_cannot_post_spam_replies()
    {
        //$this->expectException(Exception::class);
        $this->signIn();
        //$this->withExceptionHandling();

        //$this->assertTrue($this->spamFilter->detect('Yahoo'));

        $this->expectException('Exception');
        
        $this->post(route('reply.store',['thread'=>$this->thread->id]),['body'=>'sex']);
    }

    /** @test
    */
    public function a_user_cannot_post_a_reply_with_4_consecutive_same_character()
    {
        $this->signIn();
        //$this->assertFalse($this->repeatCharacterFilter->detect('Yahoo'));
        $this->expectException('Exception');
        //$this->assertTrue($this->repeatCharacterFilter->detect('Yahoooooo'));
        $this->post(route('reply.store',['thread'=>$this->thread->id]),['body'=>'Yahoooo']);
    }
}