<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Support\Facades\Mail;

class SendEmailConfirmationLinkToUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        //
        $user = $event->user;
        $user->confirmation_token = str_random(15);
        $user->save();

        Mail::to($event->user)->send(New PleaseConfirmYourEmail($event));
    }
}
