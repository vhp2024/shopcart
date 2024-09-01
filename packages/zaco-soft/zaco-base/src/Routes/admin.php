<?php
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth']], function () {
    Route::group(['prefix' => '/settings', 'as' => 'Setting::', 'namespace' => 'ZacoSoft\ZacoBase\Http\Controllers\Admin'], function () {
        Route::get('/{group?}', [
            'as' => 'index',
            'uses' => 'SettingController@index',
        ]);

        Route::post('/save', [
            'as' => 'post_save',
            'uses' => 'SettingController@post_save',
        ]);
    });

    // Route::group(['prefix' => '/menus', 'as' => 'Menu::', 'namespace' => 'ZacoSoft\ZacoBase\Controllers\Panel'], function () {
    //     Route::get("/", function(){
    //         return \View::make("zaco-base::menus.index");
    //     })->name('index');

    //     Route::get('/get-data', [
    //         'as' => 'get_data',
    //         'uses' => 'MenuController@getData',
    //     ]);

    //     Route::get('/create', [
    //         'as' => 'create',
    //         'uses' => 'MenuController@create',
    //     ]);

    //     Route::post('/save-menu', [
    //         'as' => 'post_save_menu',
    //         'uses' => 'UserController@post_save_menu',
    //     ]);
    // });

    Route::group(['prefix' => '/users', 'as' => 'User::', 'namespace' => 'ZacoSoft\ZacoBase\Http\Controllers\Admin'], function () {
        Route::get("/", function () {
            return \View::make("zaco-base::admin.users.index");
        })->name('index');

        Route::get('/get-data', [
            'as' => 'getData',
            'uses' => 'UserController@getData',
        ]);

        Route::get('/edit/{code}/{username}', [
            'as' => 'edit',
            'uses' => 'UserController@edit',
        ]);

        Route::post('/save-edit', [
            'as' => 'post_save_edit',
            'uses' => 'UserController@post_save_edit',
        ]);

        Route::post('/save-security', [
            'as' => 'postSaveSecurity',
            'uses' => 'UserController@postSaveSecurity',
        ]);
    });

    Route::group(['prefix' => '/translations', 'as' => 'Translations::', 'namespace' => 'ZacoSoft\ZacoBase\Http\Controllers\Admin'], function () {
        Route::get('view/{groupKey?}', 'TranslationController@getView')->where('groupKey', '.*');
        Route::get('/{groupKey?}', 'TranslationController@getIndex')->where('groupKey', '.*')->name('getIndex');
        Route::post('/add/{groupKey}', 'TranslationController@postAdd')->where('groupKey', '.*');
        Route::post('/edit/{groupKey}', 'TranslationController@postEdit')->where('groupKey', '.*');
        Route::post('/groups/add', 'TranslationController@postAddGroup');
        Route::post('/delete/{groupKey}/{translationKey}', 'TranslationController@postDelete')->where('groupKey', '.*');
        Route::post('/import', 'TranslationController@postImport');
        Route::post('/find', 'TranslationController@postFind');
        Route::post('/locales/add', 'TranslationController@postAddLocale');
        Route::post('/locales/remove', 'TranslationController@postRemoveLocale');
        Route::post('/publish/{groupKey}', 'TranslationController@postPublish')->where('groupKey', '.*');
        Route::post('/translate-missing', 'TranslationController@postTranslateMissing');
    });

    Route::group(['prefix' => '/role-permission', 'as' => 'RolePermission::', 'namespace' => 'ZacoSoft\ZacoBase\Http\Controllers\Admin'], function () {
        Route::get('/', [
            'as' => 'index',
            'uses' => 'RolePermissionController@index',
        ]);

        Route::post('/save', [
            'as' => 'post_save',
            'uses' => 'SettingController@post_save',
        ]);
    });

});
