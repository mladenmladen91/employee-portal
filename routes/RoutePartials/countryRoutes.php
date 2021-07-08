<?php
Route::group(
    ['prefix' => 'country/'],
    function () {
        Route::group(
            ['middleware' => 'auth:api'],
            function () {
                Route::post('addCountry', [App\Http\Controllers\CountryController::class, 'addCountry']);
                Route::post('updateParticularCountry', [App\Http\Controllers\CountryController::class, 'updateParticularCountry']);
                Route::post('removeCountry', [App\Http\Controllers\CountryController::class, 'removeCountry']);
            }
        );
        Route::get('getCountries', [App\Http\Controllers\CountryController::class, 'getCountries']);
        Route::post('getCountryCity', [App\Http\Controllers\CountryController::class, 'getCountryCity']);
        Route::post('autoCompleteCountryCity', [App\Http\Controllers\CountryController::class, 'autoCompleteCountryCity']);
    }
);
