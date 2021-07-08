<?php
Route::group(
    ['prefix' => 'worktime/'],
    function () {
Route::group(['middleware' => 'auth:api'],
            function () {
                Route::post('addWorkTime',[App\Http\Controllers\WorkTimeController::class, 'addWorkTime']); 
                Route::post('updateWorkTime',[App\Http\Controllers\WorkTimeController::class, 'updateWorkTime']); 
                Route::post('removeWorkTime',[App\Http\Controllers\WorkTimeController::class, 'removeWorkTime']);
            });
Route::get('getWorkTimes',[App\Http\Controllers\WorkTimeController::class, 'getWorkTimes']); 

});