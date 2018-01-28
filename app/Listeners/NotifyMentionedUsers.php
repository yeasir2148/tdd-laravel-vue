<?php

namespace App\Listeners;

use App\Events\NewReplyAddedToThread;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Notifications\YouWereMentioned;

class NotifyMentionedUsers
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
     * @param  NewReplyAddedToThread  $event
     * @return void
     */
    public function handle(NewReplyAddedToThread $event)
    {
                
        $users_to_notify = $this->get_users_to_notify($event->mentioned_users);

        $this->sendNotificationsToMentionedUsers($users_to_notify, $event);

        
    }

    protected function get_users_to_notify($mentioned_users)
    {
        $users_to_notify = [];
        foreach($mentioned_users as $name)
        {
            $user = User::whereName($name)->first();
            $user ? array_push($users_to_notify, $user) : false ;
        }

        return $users_to_notify;
    }

    protected function sendNotificationsToMentionedUsers($users_to_notify, $event)
    {
        foreach($users_to_notify as $notifiable)
        {
            $notifiable->notify(new YouWereMentioned($event->thread, $event->reply));
        }
    }
}
