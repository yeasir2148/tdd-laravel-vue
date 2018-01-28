<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//dd(app('ThreadFilter'));

Route::get('/', function () {
    return view('welcome');
});

############# User routes #####################

Route::get('users','UsersController@index')->middleware('auth')->name('users.all');
Route::get('register/confirm','UsersController@confirmEmail')->name('confirm.registered.email');

############# Thread routes #####################

Route::get('/threads','ThreadsController@index')->name('threads.all');
Route::get('threads/create','ThreadsController@create')->name('thread.create');
Route::get('threads/{channel}','ThreadsController@index')->name('channel.threads');
Route::get('threads/{channel}/{thread}','ThreadsController@show')->name('threads.show');

Route::post('threads/create','ThreadsController@store')->name('thread.store')->middleware('can:create,App\Thread');
Route::post('threads/{thread}/replies','RepliesController@store')->name('reply.store');
Route::post('threads/{thread}/bestReply/{reply}','ThreadsController@addBestReply')->name('thread.best-reply.store');

Route::delete('threads/{channel}/{thread}','ThreadsController@destroy')->name('thread.delete');


############# Reply routes #####################

Route::delete('replies/delete/{reply}','RepliesController@destroy')->name('reply.delete');
Route::post('reply/update/{reply}','RepliesController@update')->name('reply.update');

    //***** api routes ****
Route::get('api/{thread}/replies','ThreadsController@getPaginatedReplies')->name('replies.paginated');

############# Favorite/Like routes #############

Route::post('replies/{reply}/favorite','FavoritesController@store')->name('reply.favorite');
Route::delete('replies/favorite/delete/{favorite}','FavoritesController@destroy')->name('favorite.delete');


############# Profile routes ###################

Route::post('profile/{user}/avatar','AvatarController@store')->name('user.avatar.store')->middleware('auth');
Route::get('profile/{user}','ProfilesController@show')->name('user.profile');

############# Avatar routes ###################



############# Thread subscription routes #####################

Route::post('threads/{channel}/{thread}/subscribe','ThreadSubscriptionController@store')->name('thread.subscribe');
Route::delete('threads/{channel}/{thread}/subscribe','ThreadSubscriptionController@destory')->name('thread.unsubscribe');


############# User notification routes #####################

Route::delete('profile/{user}/notifications/{notification}','NotificationsController@destroy')->name('notification.delete');
Route::get('profile/{user}/notifications/unread','NotificationsController@index')->name('notifications.unread');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

