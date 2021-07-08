<?php
Route::group(
    ['prefix' => 'user/'],
    function () {
Route::group(['middleware' => ['auth:api', 'client']],
            function () {
                Route::post('updateParticularEducation',[App\Http\Controllers\ClientController::class, 'updateParticularEducation']); 
                Route::post('addEducation',[App\Http\Controllers\ClientController::class, 'addEducation']); 
                Route::post('removeEducation',[App\Http\Controllers\ClientController::class, 'removeEducation']); 
                Route::post('updateParticularExperience',[App\Http\Controllers\ClientController::class, 'updateParticularExperience']); 
                Route::post('addExperience',[App\Http\Controllers\ClientController::class, 'addExperience']); 
                Route::post('updateForeignLanguages',[App\Http\Controllers\ClientController::class, 'updateForeignLanguages']); 
                Route::post('removeLanguage',[App\Http\Controllers\ClientController::class, 'removeLanguage']); 
                Route::post('removeExperience',[App\Http\Controllers\ClientController::class, 'removeExperience']);
                Route::post('addComputerSkill',[App\Http\Controllers\ClientController::class, 'addComputerSkill']); 
                Route::post('removeComputerSkill',[App\Http\Controllers\ClientController::class, 'removeComputerSkill']); 
                Route::post('updateComputerSkills',[App\Http\Controllers\ClientController::class, 'updateComputerSkills']); 
                Route::post('updateCvDocument',[App\Http\Controllers\ClientController::class, 'updateCvDocument']); 
                Route::post('removeDocument',[App\Http\Controllers\ClientController::class, 'removeDocument']);
                Route::post('addVideo',[App\Http\Controllers\ClientController::class, 'addVideo']);
                Route::post('removeVideo',[App\Http\Controllers\ClientController::class, 'removeVideo']);
                Route::post('updateDriverLicence',[App\Http\Controllers\ClientController::class, 'updateDriverLicence']);
                Route::post('updateAdditionalInfo',[App\Http\Controllers\ClientController::class, 'updateAdditionalInfo']);
                Route::post('getClientAdds',[App\Http\Controllers\ClientController::class, 'getClientAdds']);
                Route::post('getDashboard',[App\Http\Controllers\ClientController::class, 'getDashboard']);
                Route::post('filterSpecialAds',[App\Http\Controllers\ClientController::class, 'filterSpecialAds']);
                Route::post('addSeen',[App\Http\Controllers\ClientController::class, 'addSeen']);
                Route::post('updateDesireJobs',[App\Http\Controllers\ClientController::class, 'updateDesireJobs']);
                Route::post('updateDesireCities',[App\Http\Controllers\ClientController::class, 'updateDesireCities']);
                Route::post('toggleFavorite',[App\Http\Controllers\ClientController::class, 'toggleFavorite']);
                Route::post('getFavorites',[App\Http\Controllers\ClientController::class, 'getFavorites']); 
                Route::post('getAdsForMe',[App\Http\Controllers\ClientController::class, 'getAdsForMe']);
            });
          

});