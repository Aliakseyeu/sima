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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::group(['middleware' => 'auth'], function () {
    Route::get('/item/create/{group}', 'ItemController@create')->where('group', '[0-9]+')->name('item.create');
    Route::post('/item/show', 'ItemController@show');
    Route::post('/order/store_exists', 'OrderController@storeExists')->name('store.exists');
    Route::post('/order/store_new', 'OrderController@storeNew');
    Route::get('/order/destroy/{id}', 'OrderController@destroy')->where('id', '[0-9]+');
    Route::get('/order/edit/{id}', 'OrderController@edit')->where('id', '[0-9]+');
    Route::post('/order/update', 'OrderController@update');
    Route::get('/archive', 'ArchiveController@index')->name('archive');
    Route::get('/delivery/update/{id}', 'DeliveryController@update')->where('id', '[0-9]+');
    Route::get('/item/update/{id}', 'ItemController@update')->where('id', '[0-9]+');
    Route::get('/report/show/{id}', 'ReportController@show')->where('id', '[0-9]+');
    Route::get('/user', 'UserController@edit');
    Route::post('/user/update', 'UserController@update');

    Route::group(['middleware' => 'role:admin'], function () {
        Route::get('/group/store', 'GroupController@store');
        Route::get('/group/toCart/{id}', 'GroupController@toCart')->where('id', '[0-9]+');
        Route::get('/group/destroy/{id}', 'GroupController@destroy')->where('id', '[0-9]+');
        Route::get('/group/{id}/show', 'GroupController@show')->where('id', '[0-9]+');
        Route::post('/group/update', 'GroupController@update');
        Route::get('/archive/store/{id}', 'ArchiveController@store')->where('id', '[0-9]+');
    });
});

Auth::routes();

Route::get('/', 'IndexController@index')->name('index');


