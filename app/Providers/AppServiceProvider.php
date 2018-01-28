<?php

namespace App\Providers;

use Schema;
use View;
use App\Channel;
use App\Filters\ThreadFilter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
//use App\Rule\SpamValidator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

        //$channels = Channel::all();
        \View::composer('*',function($view) {
            $view->with('channels',Channel::all());
        });

        // View::share('channels', $channels);


        //customer validation rule bootup

        \Validator::extend('spamFree','App\Rules\SpamValidator@passes');

        Relation::morphMap([
            'Thread' => 'App\Thread',
            'Reply' => 'App\Reply',
            'Favorite'=>'App\Favorite',
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //

        if($this->app->isLocal())
        {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }

        /*\App::bind(ThreadFilter::class, function(){
            return new \App\Filters\ThreadFilter;
        });*/
    }
}
