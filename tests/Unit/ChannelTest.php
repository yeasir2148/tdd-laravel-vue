<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Channel;
use App\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ChannelTest extends TestCase
{
    use DatabaseMigrations;

    /** @test
    */

    function a_channel_has_threads()
    {
        $channel = make(Channel::class,[],2);
        //dd($channel);
        $thread = make(Thread::class);
    }
}