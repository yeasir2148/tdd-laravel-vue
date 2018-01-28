<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Storage;

class AvatarController extends Controller
{
    //

    public function store($user, Request $request)
    {
        //dd($user);
        $this->validate($request, [
            'avatar' => 'required | image',
        ]);
        
        if($request->file('avatar')->isValid())
        {    
            
           $new_file_name = $this->uploadFile($request);

           if($new_file_name)
           {
                $user = auth()->user();
                $user->avatar_location = $new_file_name;
                $user->save();
           }

        }

        return back();
    }

    protected function uploadFile($request)
    {
        $new_file_name = Storage::disk('public')
                        ->putFile('avatars',$request->file('avatar'));
        
        return $new_file_name;

    }
}
