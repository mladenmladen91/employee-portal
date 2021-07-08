<?php
Route::group(
    ['prefix' => 'contact/'],
    function () {
        Route::post('sendMessage', [App\Http\Controllers\ContactMessageController::class, 'sendMessage']);
    }
);
