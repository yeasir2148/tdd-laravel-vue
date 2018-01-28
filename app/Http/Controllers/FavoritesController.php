<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Reply;
use App\Favorite;

class FavoritesController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Reply $reply)
    {
        
        $reply->addAsFavorite();

        if(request()->ajax())
        {
            return response()->json($reply->fresh());
        }

        return redirect()->back();
    }

    public function destroy(Favorite $favorite)
    {
        //dd(request()->path());
        $reply = $favorite->favorited;
        $deleted = $favorite->delete();

        //dd($deleted);

        return response()->json($reply->fresh());
    }
}
