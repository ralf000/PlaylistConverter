<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', function () {
    return \App\Http\Controllers\IndexController::run();
})->middleware('auth.basic');


Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'init']], function () {

    // admin
    Route::get('/', function () {
        if (view()->exists('admin.index')) {
            $data = ['title' => 'Панеля администратора'];
            return view('admin.index', $data);
        }
        return view('errors.404');

    });

    /**
     * Синхронизировать с плейлистом
     */
    Route::post('/update-from-playlist', function () {
        return \App\Http\Controllers\PlaylistController::syncWithPlaylist();
    })->name('update-from-playlist');

    /**
     * Сбросить все данные из плейлиста
     */
    Route::post('/reset-playlist', function () {
        return \App\Http\Controllers\PlaylistController::resetPlaylist();
    })->name('reset-playlist');

    /**
     * admin/config
     */
    Route::group(['prefix' => 'config'], function () {

        Route::get('/', [
            'uses' => 'ConfigController@index',
            'as' => 'config'
        ]);

        Route::post('/', [
            'uses' => 'ConfigController@update',
            'as' => 'config-update'
        ]);

    });

    /**
     * admin/groups
     */
    Route::group(['prefix' => 'groups'], function () {

        Route::put('/', [
            'uses' => 'ChannelGroupController@store',
            'as' => 'groups-store'
        ]);

        Route::post('/', [
            'uses' => 'ChannelGroupController@update',
            'as' => 'groups-update'
        ]);

        Route::delete('/', [
            'uses' => 'ChannelGroupController@destroy',
            'as' => 'groups-delete'
        ]);

    });

    /**
     * admin/channels
     */
    Route::group(['prefix' => 'channels'], function () {

        Route::get('/', [
            'uses' => 'ChannelsController@index',
            'as' => 'channels'
        ]);

        Route::put('/', [
            'uses' => 'ChannelsController@store',
            'as' => 'channels-store'
        ]);

        Route::post('/', [
            'uses' => 'ChannelsController@update',
            'as' => 'channels-update'
        ]);

        Route::delete('/', [
            'uses' => 'ChannelsController@destroy',
            'as' => 'channel-delete'
        ]);

    });


    /**
     * Для ajax запросов
     */
    Route::group(['prefix' => 'ajax'], function () {

        Route::post('/change-group-visibility', [
            'uses' => 'ChannelGroupController@changeGroupVisibility',
            'as' => 'change-group-visibility'
        ]);

        Route::post('/change-channel-visibility', [
            'uses' => 'ChannelsController@changeChannelVisibility',
            'as' => 'change-channel-visibility'
        ]);

    });

});

