<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'account'], function ()
{
    Route::get('accept/{token}', [UserController::class, 'accept']);
    Route::get('reset/{token}', [UserController::class, 'reset'])->name('reset');
    Route::post('reset/{token}', [UserController::class, 'resetPost']);
});