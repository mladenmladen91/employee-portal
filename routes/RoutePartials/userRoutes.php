<?php
Route::group(
    ['prefix' => 'user/'],
    function () {
        Route::group(
            ['middleware' => 'auth:api'],
            function () {
                Route::post('getinfo', [App\Http\Controllers\UserController::class, 'getInfo']);
                Route::post('updateUser', [App\Http\Controllers\UserController::class, 'updateUser']);
                Route::post('deactivate', [App\Http\Controllers\UserController::class, 'deactivate']);
                Route::post('activate', [App\Http\Controllers\UserController::class, 'activate']);
                Route::post('profileImage', [App\Http\Controllers\UserController::class, 'profileImage']);
                Route::post('backgroundImage', [App\Http\Controllers\UserController::class, 'backgroundImage']);
                Route::post('profileVideo', [App\Http\Controllers\UserController::class, 'profileVideo']);
                Route::post('getUsers', [App\Http\Controllers\UserController::class, 'getUsers']);
                Route::post('archiveUsers', [App\Http\Controllers\UserController::class, 'archiveUsers']);
                Route::post('deleteUsers', [App\Http\Controllers\UserController::class, 'deleteUsers']);
                Route::post('resetUsers', [App\Http\Controllers\UserController::class, 'deleteUsers']);
                Route::post('toggleUser', [App\Http\Controllers\UserController::class, 'toggleUser']);
                Route::post('createUser', [App\Http\Controllers\UserController::class, 'createUser']);
                Route::post('getUser', [App\Http\Controllers\UserController::class, 'getUser']);
                Route::post('createMessage', [App\Http\Controllers\UserController::class, 'createMessage']);
                Route::post('sendMessages',[App\Http\Controllers\UserController::class, 'sendMessages']);
                Route::post('updateParticularMessage', [App\Http\Controllers\UserController::class, 'updateParticularMessage']);
                Route::post('updateParticularNotification', [App\Http\Controllers\UserController::class, 'updateParticularNotification']);
                Route::post('createNotification', [App\Http\Controllers\UserController::class, 'createNotification']);
                Route::post('updateAdminUser', [App\Http\Controllers\UserController::class, 'updateAdminUser']);
                Route::post('getAdminDashboard', [App\Http\Controllers\UserController::class, 'getAdminDashboard']);
                Route::post('updateAdminParticularEducation', [App\Http\Controllers\UserController::class, 'updateAdminParticularEducation']);
                Route::post('updateAdminParticularExperience', [App\Http\Controllers\UserController::class, 'updateAdminParticularExperience']);
                Route::post('addAdminExperience', [App\Http\Controllers\UserController::class, 'addAdminExperience']);
                Route::post('removeAdminExperience', [App\Http\Controllers\UserController::class, 'removeAdminExperience']);
                Route::post('updateAdminForeignLanguages', [App\Http\Controllers\UserController::class, 'updateAdminForeignLanguages']);
                Route::post('removeAdminLanguage', [App\Http\Controllers\UserController::class, 'removeAdminLanguage']);
                Route::post('updateAdminComputerSkills', [App\Http\Controllers\UserController::class, 'updateAdminComputerSkills']);
                Route::post('addAdminComputerSkill', [App\Http\Controllers\ClientController::class, 'addAdminComputerSkill']);
                Route::post('updateAdminCvDocument', [App\Http\Controllers\UserController::class, 'updateAdminCvDocument']);
                Route::post('removeAdminVideo', [App\Http\Controllers\UserController::class, 'removeAdminVideo']);
                Route::post('updateAdminVideo', [App\Http\Controllers\UserController::class, 'updateAdminVideo']);
                Route::post('updateAdminDriverLicence', [App\Http\Controllers\UserController::class, 'updateAdminDriverLicence']);
                Route::post('updateAdminAdditionalInfo', [App\Http\Controllers\UserController::class, 'updateAdminAdditionalInfo']);
                Route::post('profileAdminImage', [App\Http\Controllers\UserController::class, 'profileAdminImage']);
                Route::post('getMessages', [App\Http\Controllers\UserController::class, 'getMessages']);
                Route::post('getSingleMessage', [App\Http\Controllers\UserController::class, 'getSingleMessage']);
                Route::post('getNotifications', [App\Http\Controllers\UserController::class, 'getNotifications']);
                Route::post('getSingleNotification', [App\Http\Controllers\UserController::class, 'getSingleNotification']);
                Route::post('viewMessage', [App\Http\Controllers\UserController::class, 'viewMessage']);
                Route::post('viewNotification', [App\Http\Controllers\UserController::class, 'viewNotification']);
                Route::post('updateSettingNotification', [App\Http\Controllers\UserController::class, 'updateSettingNotification']);
                Route::post('getConversation', [App\Http\Controllers\UserController::class, 'getConversation']);
                Route::post('changePassword', [App\Http\Controllers\PassportAuthController::class, 'changePassword']);
            }
        );
        Route::get('getGenders', [App\Http\Controllers\UserController::class, 'getGenders']);
        Route::get('getEducationLevels', [App\Http\Controllers\UserController::class, 'getEducationLevels']);
        Route::get('getAllAddings', [App\Http\Controllers\UserController::class, 'getAllAddings']);
        Route::post('downloadMedia', [App\Http\Controllers\UserController::class, 'downloadMedia']);
        Route::get('getEminentEmployers', [App\Http\Controllers\UserController::class, 'getEminentEmployers']);
        Route::get('getJobsData', [App\Http\Controllers\UserController::class, 'getJobsData']);
    }
);
