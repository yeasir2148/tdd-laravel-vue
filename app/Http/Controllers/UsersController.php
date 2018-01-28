<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UsersController extends Controller
{
    //

    public function index(Request $request)
    {
        //return ($request->q);
        $users = User::where('name','like',$request->q."%")->take(5)->get();
        return json_encode($users);
    }

    public function canUpdate($auth_user, $profile_user)
    {
        
    }

    public function confirmEmail(Request $request)
    {
        //dd($request->confirmation_token);
        try{
            $user = User::whereNotNull('confirmation_token')->where('confirmation_token', $request->confirmation_token)->firstOrFail();
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $exception){
            return redirect('threads')->with('flash','Invalid confirmation token');
        }

        $user->confirmed = true;
        $user->confirmation_token = null;
        $user->save();

        return redirect('home')->with('flash','Thank you for confirming your email');

    }
}
