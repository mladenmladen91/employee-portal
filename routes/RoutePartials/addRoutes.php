<?php
Route::group(
    ['prefix' => 'jobs/'],
    function () {
Route::group(['middleware' => ['auth:api']],
            function () {
                // route for archiving ad
                Route::post('archiveAds',[App\Http\Controllers\AddControler::class, 'archiveAds']); 
                // route for creating ad
                Route::post('createAd',[App\Http\Controllers\AddControler::class, 'createAd']);
                // route for activating ad
                Route::post('setAd',[App\Http\Controllers\AddControler::class, 'setAd']);
                // route for getting company ads -admin part
                Route::post('getAdminCompaniesAds',[App\Http\Controllers\AddControler::class, 'getAdminCompaniesAds']); 
                // route for updating add admin adn company founder
                Route::post('updateAd',[App\Http\Controllers\AddControler::class, 'updateAd']);
                // route for answering ad questions
                Route::post('answerQuestion',[App\Http\Controllers\AddControler::class, 'answerQuestion']);
                // route for removing ad answers
                Route::post('removeAnswer',[App\Http\Controllers\AddControler::class, 'removeAnswer']);
                // route for applying an ad
                Route::post('adApply',[App\Http\Controllers\AddControler::class, 'adApply']);
                // route for saving an ad
                Route::post('adSavedApply',[App\Http\Controllers\AddControler::class, 'adSavedApply']);
                // route for viewing applicant
                Route::post('viewApplicant',[App\Http\Controllers\AddControler::class, 'viewApplicant']);
                // toggle active ad
			    Route::post('toggleActiveAd',[App\Http\Controllers\AddControler::class, 'toggleActiveAd']);
                // multiple company ads delete
                Route::post('deleteAds',[App\Http\Controllers\AddControler::class, 'deleteAds']);
                // delete ad, admin or company
                Route::post('deleteAd',[App\Http\Controllers\AddControler::class, 'deleteAd']);
                // view applicants from an ad
                Route::post('viewApplicants',[App\Http\Controllers\AddControler::class, 'viewApplicants']);
                // route for favoring user in applying
                Route::post('addSelected',[App\Http\Controllers\AddControler::class, 'addSelected']);
                // route for removing selected user in applying
                Route::post('removeSelected',[App\Http\Controllers\AddControler::class, 'removeSelected']);
                // view selected applicants
                Route::post('viewSelectedApplicants',[App\Http\Controllers\AddControler::class, 'viewSelectedApplicants']);
                // toggle active ad
			    Route::post('resetAd',[App\Http\Controllers\AddControler::class, 'resetAd']);
                // get ad details with logged user
                Route::post('getAuthAd',[App\Http\Controllers\AddControler::class, 'getAuthAd']); 
            });
            Route::post('getAd',[App\Http\Controllers\AddControler::class, 'getAd']);           
            Route::post('getAllAds',[App\Http\Controllers\AddControler::class, 'getAds']);  
            Route::post('getCompaniesAds',[App\Http\Controllers\AddControler::class, 'getCompaniesAds']); 
            Route::post('keywordAutoComplete',[App\Http\Controllers\AddControler::class, 'keywordAutoComplete']); 
});