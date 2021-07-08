<?php
Route::group(
    ['prefix' => 'city/'],
    function () {
        Route::group(
            ['middleware' => 'auth:api'],
            function () {
                Route::post('addCity', [App\Http\Controllers\CityController::class, 'addCity']);
                Route::post('updateParticularCity', [App\Http\Controllers\CityController::class, 'updateParticularCity']);
                Route::post('removeCity', [App\Http\Controllers\CityController::class, 'removeCity']);
            }
        );
        Route::post('searchCities', [App\Http\Controllers\CityController::class, 'searchCities']);
        Route::get('getCities', [App\Http\Controllers\CityController::class, 'getCities']);
    }
);
