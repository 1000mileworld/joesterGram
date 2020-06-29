<?php

use Illuminate\Support\Facades\Route;
use App\Mail\NewUserWelcomeMail;
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

Auth::routes();

Route::get('/email', function() {
    return new NewUserWelcomeMail();
});

Route::post('follow/{user}', 'FollowsController@store');


Route::get('/',function(){
    return view('welcome');
});

Route::get('/feed', 'PostsController@index');
Route::get('/p/create', 'PostsController@create'); //order must be first, as create could be considered a {post} and this line won't be executed
Route::post('/p', 'PostsController@store');
Route::get('/p/{post}', 'PostsController@show'); //best practice to leave dynamic routes at end


Route::get('/profile', function() {
    return redirect()->route('profile.show', ['user' => auth()->user()->id]);
});

//Route::get('/profile/{id}', ...)->name('profile.show');


Route::get('/profile/{user}', 'ProfilesController@index')->name('profile.show');
Route::get('/profile/{user}/edit', 'ProfilesController@edit')->name('profile.edit');
Route::patch('/profile/{user}', 'ProfilesController@update')->name('profile.update');
