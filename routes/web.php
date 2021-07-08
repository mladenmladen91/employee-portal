<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\CompanyController;

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


// route for getting user token
Route::group(
            ['middleware' => 'auth'],
            function () {

            });

Route::get('oauth/token', [App\Http\Controllers\PassportAuthController::class, 'token']);
