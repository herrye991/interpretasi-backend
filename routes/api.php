<?php

use App\Http\Controllers\v1\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\AuthController;
use App\Http\Controllers\v1\CommentController;
use App\Http\Controllers\v1\UserController;
use App\Http\Controllers\v1\LikeController;
use App\Http\Controllers\v1\ReportController;
use App\Http\Controllers\v1\TestController;
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
        /** Signup */
        Route::prefix('signup')->group(function () {
            Route::post('/', [AuthController::class, 'signup']);
            Route::post('/resend', [AuthController::class, 'resend'])->middleware('auth:api');
        });
        /** Signin */
        Route::post('signin', [AuthController::class, 'signin']);
        /** Signout*/
        Route::post('signout', [AuthController::class, 'signout'])->middleware('auth:api');
        /** Password Reset */
        Route::post('password-reset', [AuthController::class, 'reset']);
        /** Google Signin  */
        Route::post('signin/{provider}', [AuthController::class, 'oauth']);
        
        Route::group(['prefix' => 'user'], function() {
            Route::get('/{id}/show', [UserController::class, 'show']);
        });
        
        /** Midleware Auth */
        Route::group(['middleware' => ['auth:api', 'email-verify.checker']], function ()
        {
            /** User */
            Route::prefix('user')->group(function () {
                Route::post('/{id}/report', [ReportController::class, 'user']);
                Route::prefix('articles')->group(function () {
                    Route::get('/', [UserController::class, 'articles']);
                    Route::get('/{type}', [UserController::class, 'articlesType']);
                });
                Route::get('check', [UserController::class, 'check']);
                /** User/Password */
                Route::prefix('password')->group(function () {
                    Route::post('add', [UserController::class, 'setPassword']);
                    Route::post('change', [UserController::class, 'changePassword']);
                });
                /** User/Profile */
                Route::prefix('profile')->group(function () {
                    Route::get('/', [UserController::class, 'getProfile']);
                    Route::post('/update', [UserController::class, 'updateProfile']);
                });
            });
        });
        /** Article */
        Route::apiResource('article', ArticleController::class);
        Route::prefix('article')->group(function () {
            /** By Tag */
            Route::get('tag/{tag}', [ArticleController::class, 'tag']);
            /** Upload Image */
            Route::post('upload-image', [ArticleController::class, 'uploadImage']);
            /** Report */
            Route::post('{url}/report', [ReportController::class, 'article']);
            /** Artticle/Comment */
            Route::get('{url}/comment', [CommentController::class, 'index']);
            Route::post('{url}/comment', [CommentController::class, 'store']);
            Route::delete('{url}/comment/{id}', [CommentController::class, 'destroy']);
            /** Article/Like */
            Route::get('{url}/like', [LikeController::class, 'index']);
            Route::post('{url}/like', [LikeController::class, 'store']);
        });
    });