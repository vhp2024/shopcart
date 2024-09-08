<?php
Route::group(['middleware' => ['api'], 'namespace' => 'ZacoSoft\ZacoBase\Http\Controllers'], function () {

    Route::get('/pimo/config', [
        'as' => 'getConfig',
        'uses' => 'PimoController@getConfig',
    ]);

    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', [
            'as' => 'postLogin',
            'uses' => 'AuthController@postLogin',
        ]);

        Route::post('/register', [
            'as' => 'postRegister',
            'uses' => 'AuthController@postRegister',
        ]);

        Route::post('/forget-password', [
            'as' => 'postForgetPassword',
            'uses' => 'AuthController@postForgetPassword',
        ]);

        Route::post('/reset-password', [
            'as' => 'postResetNewPassword',
            'uses' => 'AuthController@postResetNewPassword',
        ]);

        Route::group(['middleware' => ['auth:sanctum']], function () {
            Route::get('/me', [
                'as' => 'getMe',
                'uses' => 'AuthController@getMe',
            ]);
        });
    });

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::group(['prefix' => 'account-settings'], function () {
            Route::group(['prefix' => 'security'], function () {
                Route::get('/get-otp', [
                    'as' => 'getOtp',
                    'uses' => 'AccountSettings\SecurityController@getOtp',
                ]);

                Route::post('/post-otp', [
                    'as' => 'postOtp',
                    'uses' => 'AccountSettings\SecurityController@postOtp',
                ]);

                Route::post('/delete-otp', [
                    'as' => 'postDeleteOtp',
                    'uses' => 'AccountSettings\SecurityController@postDeleteOtp',
                ]);

                Route::post('/post-check-otp', [
                    'as' => 'postCheckOtp',
                    'uses' => 'AccountSettings\SecurityController@postCheckOtp',
                ]);
            });
        });

        Route::group(['prefix' => 'admin'], function () {
            Route::group(['prefix' => 'user'], function () {
                Route::get('/get-list', [
                    'as' => 'getList',
                    'uses' => 'Admin\UserController@getList',
                ]);
            });
        });
    });

});
