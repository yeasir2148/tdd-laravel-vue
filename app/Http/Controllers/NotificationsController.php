<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;


class NotificationsController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(User $user)
    {
        return auth()->user()->unreadNotifications;
    }

    public function destroy(User $user, $notification_id)
    {
        auth()->user()->unreadNotifications()->find($notification_id)->markAsRead();
    }
}
