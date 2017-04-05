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

Route::get('/', function () {
    return view('welcome');
})->middleware('auth.basic');

Auth::routes();

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {

    // admin
    Route::get('/', function () {
        if (view()->exists('admin.index')) {
            $data = ['title' => 'Панеля администратора'];
            return view('admin.index', $data);
        }
        return view('errors.404');

    });

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

        Route::get('/', [
            'uses' => 'ChannelGroupController@index',
            'as' => 'groups'
        ]);

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
     *
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
            'as' => 'channels-delete'
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

    });

});

