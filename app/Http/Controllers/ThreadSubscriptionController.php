<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Thread;

class ThreadSubscriptionController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store($channel_slug, Thread $thread)
    {
        $thread->subscribe();
    }

    public function destory($channel_slug, Thread $thread)
    {
        $thread->subscriptions()->where('user_id',auth()->user()->id)->delete();
    }
}
