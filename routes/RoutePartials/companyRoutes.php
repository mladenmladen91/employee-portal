<?php

use App\Http\Controllers\CompanyController;

Route::group(
    ['prefix' => 'user/'],
    function () {
        Route::group(['middleware' => ['auth:api', 'company']],
            function () {
                Route::post('updateCompany',[CompanyController::class, 'updateCompany']);
                Route::post('addCompanyUser',[CompanyController::class, 'addCompanyUser']);
                Route::post('updateParticularCompanyUser',[CompanyController::class, 'updateParticularCompanyUser']);
                Route::post('removeCompanyUser',[CompanyController::class, 'removeCompanyUser']);
                Route::post('addCompanyGallery',[CompanyController::class, 'addCompanyGallery']);
                Route::post('removeCompanyGallery',[CompanyController::class, 'removeCompanyGallery']);
                Route::post('addGalleryImage',[CompanyController::class, 'addGalleryImage']);
                Route::post('removeGalleryImage',[CompanyController::class, 'removeGalleryImage']);
                Route::post('addCompanyBlog',[CompanyController::class, 'addCompanyBlog']);
                Route::post('removeCompanyBlog',[CompanyController::class, 'removeCompanyBlog']);
                Route::post('addBlogImage',[CompanyController::class, 'addBlogImage']);
                Route::post('removeBlogImage',[CompanyController::class, 'removeBlogImage']);
                Route::post('addSocialMedia',[CompanyController::class, 'addSocialMedia']);
                Route::post('updateParticularSocialMedia',[CompanyController::class, 'updateParticularSocialMedia']);
                Route::post('removeSocialMedia',[CompanyController::class, 'removeSocialMedia']);
                Route::post('addPackage',[CompanyController::class, 'addPackage']);
                Route::post('getParticularPackage',[CompanyController::class, 'getParticularPackage']);
                Route::post('updateParticularSocialPackage',[CompanyController::class, 'updateParticularSocialPackage']);
                Route::post('removePackage',[CompanyController::class, 'removePackage']);
                Route::post('addPackagePurchaseHistory',[CompanyController::class, 'addPackagePurchaseHistory']);
                Route::post('removePackagePurchaseHistory',[CompanyController::class, 'removePackagePurchaseHistory']);
                Route::post('getPublishedAds',[CompanyController::class, 'getPublishedAds']);
                Route::post('getArhivedAds',[CompanyController::class, 'getArhivedAds']);
                Route::post('getSavedAds',[CompanyController::class, 'getSavedAds']);
                Route::post('getParticularAd',[CompanyController::class, 'getParticularAd']);
                Route::post('getCompanyDashboard',[CompanyController::class, 'getCompanyDashboard']);
                Route::post('updateSeen',[CompanyController::class, 'updateSeen']);
                Route::post('getPersonalAds',[App\Http\Controllers\CompanyControler::class, 'getPersonalAds']); 
                Route::post('setReminder',[App\Http\Controllers\CompanyControler::class, 'setReminder']); 
           });
            Route::post('getCompanies',[CompanyController::class, 'getCompanies']);
            Route::post('getCompany',[CompanyController::class, 'getCompany']);
});
