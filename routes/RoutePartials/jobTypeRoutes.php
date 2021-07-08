<?php
Route::group(
    ['prefix' => 'jobtype/'],
    function () {
Route::group(['middleware' => 'auth:api'],
            function () {
                Route::post('addJobType',[App\Http\Controllers\JobTypeController::class, 'addJobType']); 
                Route::post('updateJobType',[App\Http\Controllers\JobTypeController::class, 'updateJobType']); 
                Route::post('removeJobType',[App\Http\Controllers\JobTypeController::class, 'removeJobType']);
            });
Route::get('getJobTypes',[App\Http\Controllers\JobTypeController::class, 'getJobTypes']); 
Route::post('getJobTypesByTypeOfWork',[App\Http\Controllers\JobTypeController::class, 'getJobTypesByTypeOfWork']);            

});