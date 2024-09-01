<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => '/auth', 'as' => 'Auth::', 'namespace' => 'ZacoSoft\ZacoBase\Http\Controllers'], function () {
        Route::get("/login", function () {
            return \View::make("zaco-base::auth.login");
        })->name('login');

        Route::post('/login', [
            'as' => 'postLogin',
            'uses' => 'AuthController@postLogin',
        ]);

        Route::get("/register", function () {
            return \View::make("zaco-base::auth.register");
        })->name('register');

        Route::post('/register', [
            'as' => 'postRegister',
            'uses' => 'AuthController@postRegister',
        ]);

        Route::get("/forget-password", function () {
            return \View::make("zaco-base::auth.forget-password");
        })->name('forget_password');

        Route::post('/forget-password', [
            'as' => 'postForgetPassword',
            'uses' => 'AuthController@postForgetPassword',
        ]);

        Route::get("/reset-new-password/{user_token?}", function ($user_token) {
            return \View::make("zaco-base::auth.reset-new-password", ['user_token' => $user_token]);
        })->name('reset_new_password');

        Route::post('/reset-new-password', [
            'as' => 'postResetNewPassword',
            'uses' => 'AuthController@postResetNewPassword',
        ]);
    });

    Route::group(['prefix' => '/profile', 'as' => 'Profile::', 'namespace' => 'ZacoSoft\ZacoBase\Http\Controllers', 'middleware' => ['web']], function () {
        Route::get('/{group?}', [
            'as' => 'index',
            'uses' => 'ProfileController@index',
        ]);

        Route::post('/save', [
            'as' => 'post_save',
            'uses' => 'ProfileController@post_save',
        ]);

        Route::post('/save-password', [
            'as' => 'post_save_password',
            'uses' => 'ProfileController@post_save_password',
        ]);

        Route::post('/save-security', [
            'as' => 'postSaveSecurity',
            'uses' => 'ProfileController@postSaveSecurity',
        ]);

    });

    Route::group(['prefix' => '/test', 'as' => 'Auth::', 'namespace' => 'ZacoSoft\ZacoBase\Http\Controllers'], function () {
        Route::get("/test", [
            'as' => 'test',
            'uses' => 'AuthController@test',
        ]);

    });
});
