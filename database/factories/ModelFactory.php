<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'confirmed' => false,
    ];
});

$factory->define(App\Thread::class, function (Faker\Generator $faker) {
    
    return [
        'user_id'=>factory('App\User')->create()->id,
        'channel_id'=>factory('App\Channel')->create()->id,
        'title'=>$faker->sentence,
        'body'=>$faker->paragraph,
        
    ];
});

$factory->define(App\Channel::class,function(Faker\Generator $faker){
    $name = $faker->word;
    return [
        'name'=>$name,
        'slug'=>$name,
    ];
});


$factory->define(App\Reply::class, function (Faker\Generator $faker) {
    $thread = factory('App\Thread')->create();
    return [
        'user_id'=>factory('App\User')->create()->id,
        'thread_id'=>$thread->id,
        'thread_owner'=>$thread->user_id,
        'body'=>$faker->paragraph,
    ];
});

$factory->define(App\Favorite::class,function(Faker\Generator $faker){
    return [
        'user_id'=>factory('App\User')->create()->id,
        'favorited_id'=>factory('App\User')->create()->id,
        'favorited_type'=>'Reply',
    ];
});

$factory->define(Illuminate\Notifications\DatabaseNotification::class,function(Faker\Generator $faker){
    return [
        'id' => app(Ramsey\Uuid\UuidFactory::class)->uuid4()->toString(),
        'type'=> 'App\Notifications\ThreadWasUpdated',
        'notifiable_id' => auth()->user()?: factory('App\User')->create()->id,
        'notifyable_type' => 'App\User',
        'data' => ['foo_key'=>'bar'],
    ];
});