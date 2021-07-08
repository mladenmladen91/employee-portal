<?php

require_once base_path("routes/RoutePartials/userRoutes.php");
require_once base_path("routes/RoutePartials/clientRoutes.php");
require_once base_path("routes/RoutePartials/addRoutes.php");
require_once base_path("routes/RoutePartials/companyRoutes.php");
require_once base_path("routes/RoutePartials/countryRoutes.php");
require_once base_path("routes/RoutePartials/cityRoutes.php");
require_once base_path("routes/RoutePartials/workTimeRoutes.php");
require_once base_path("routes/RoutePartials/jobTypeRoutes.php");
require_once base_path("routes/RoutePartials/contactRoutes.php");

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

// route for logging user
Route::post('login', [App\Http\Controllers\PassportAuthController::class, 'login']);
Route::post('googleLogin', [App\Http\Controllers\PassportAuthController::class, 'googleLogin']);
Route::post('facebookLogin', [App\Http\Controllers\PassportAuthController::class, 'facebookLogin']);
Route::post('appleLogin', [App\Http\Controllers\PassportAuthController::class, 'appleLogin']);
Route::post('linkedinLogin', [App\Http\Controllers\PassportAuthController::class, 'linkedinLogin']);
Route::post('logout', [App\Http\Controllers\PassportAuthController::class, 'logout']);
Route::post('register', [App\Http\Controllers\PassportAuthController::class, 'register']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
