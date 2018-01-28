<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Activity;

class ProfilesController extends Controller
{
    //
    public function show(User $user)
    {
        //dd($user);
        $profile_user = $user;
        //$profile_user = $user->load(['threads','activities']);
        /*$user_activities = $profile_user->activities()->latest()->get()->groupBy(function($item, $key){
            //dd($item);
            return $item->created_at->format('Y-m-d');
        });*/

        $user_activities = Activity::feed($profile_user);

        //return $user_activities;
        //return $profile_user;
        return view('profile.activity.user-profile',compact('profile_user','user_activities'));
    }

}
