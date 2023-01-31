<?php

use App\Http\Controllers\API\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\TestController;
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
    Route::get('test', [TestController::class, 'index']);
    Route::get('datetime', function ()
    {
        return response()->json([Carbon\Carbon::now()->format('Y-m-d H:i')]);
    });
    /** Middleware Json Only */
    Route::group(['middleware' => 'json.only'], function (){
        /**  Signup */
        Route::post('signup', [AuthController::class, 'signup']);
        /**  Google Signin  */
        Route::post('signin/{provider}', [AuthController::class, 'oauth']);
        /** Signout*/
        /** Midleware Auth */
        Route::group(['middleware' => 'auth:api', 'middleware' => 'email-verify.checker'], function ()
        {
            Route::post('signout', [AuthController::class, 'signout']);
            Route::apiResource('user', UserController::class)->only(['index']);
            Route::get('user/my-articles', [UserController::class, 'myArticles']);
            Route::post('user/set_password', [UserController::class, 'setPassword']);
            Route::post('user/change_password', [UserController::class, 'changePassword']);
            Route::get('user/check', [UserController::class, 'check']);
        });
        /** Articles */
        Route::apiResource('article', ArticleController::class);
        /** End Articles */
        /** Comments */
        Route::get('article/{url}/comment', [CommentController::class, 'index']);
        Route::post('article/{url}/comment', [CommentController::class, 'store']);
        Route::delete('article/{url}/comment/{id}', [CommentController::class, 'destroy']);
        /** End Comment */
        /** Likes */
        Route::get('article/{url}/like', [LikeController::class, 'index']);
        Route::post('article/{url}/like', [LikeController::class, 'store']);
        /** End Likes */
    });