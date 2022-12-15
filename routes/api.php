<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');

//Send the link to Verify
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

//Resend the link to verify
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/success', function () {
    return view('mail');
});

Route::get('feedback/index', 'Api\FeedbackController@index');




Route::group(['middleware'=>'auth:api'],function() { //Routes yang bisa dijalankan setelah login
    //routes untuk user
    Route::get('user', 'Api\AuthController@index');
    Route::get('user/{id}','Api\AuthController@show');
    Route::put('user/{id}','Api\AuthController@update');
    Route::delete('user/{id}','Api\AuthController@destroy');

    //posting routes
    Route::get('post/{id}', 'Api\PostController@show');
    Route::get('post', 'Api\PostController@showAll');
    Route::get('postfrom/{id}', 'Api\PostController@showUserPosts');
    Route::delete('post/{id}', 'Api\PostController@destroy');
    Route::post('post', 'Api\PostController@store');
    Route::put('post/{id}', 'Api\PostController@update');



    //comment routes
    Route::get('comment', 'Api\CommentController@index');
    Route::get('comment/{id}', 'Api\CommentController@show');
    Route::get('comment/post/{post_id}', 'Api\CommentController@showInPost');
    Route::post('comment', 'Api\CommentController@store');
    Route::post('comment/{post_id}/{user_id}', 'Api\CommentController@storeInPost');
    Route::put('comment/{id}', 'Api\CommentController@update');
    Route::delete('comment/{id}', 'Api\CommentController@destroy');
    Route::delete('comment/post/{idPost}', 'Api\CommentController@destroyPost');



    //logout routes
    Route::get('logout','Api\AuthController@logout');
});
