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

    // admin/config
    Route::get('/config', [
        'uses' => 'ConfigController@index',
        'as' => 'config'
    ]);

    Route::post('/config', [
        'uses' => 'ConfigController@update',
        'as' => 'config-update'
    ]);

    // admin/groups
    Route::get('/groups', [
        'uses' => 'ChannelGroupController@index',
        'as' => 'groups'
    ]);

    Route::put('/groups', [
        'uses' => 'ChannelGroupController@store',
        'as' => 'groups-store'
    ]);

    Route::post('/groups', [
        'uses' => 'ChannelGroupController@update',
        'as' => 'groups-update'
    ]);

    Route::delete('/groups', [
        'uses' => 'ChannelGroupController@destroy',
        'as' => 'groups-delete'
    ]);

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

