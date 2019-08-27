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

Route::get('/', 'PagesController@home');
Route::get('/', 'PagesController@dashboard');
Route::get('/', 'PagesController@watchlist');
Route::get('/', 'PagesController@demotrader');

// API
Route::get('/docs/api', 'PagesController@docsapi');

Route::group(['prefix' => 'api/v1' , 'middleware' => 'auth'], function () {

    Route::post('/stock/buy', 'TransactionController@store');
    Route::post('/stock/sell', 'TransactionController@sell');

    Route::get('/user/watchlist/add/{symbol}', 'UserController@addToWatchlist');
    Route::get('/user/watchlist/remove/{symbol}', 'UserController@removeFromWatchlist');

    Route::post('/user/isLoggedIn', 'UserController@isLoggedIn');
    Route::post('/user/logout', 'UserController@logout');

    Route::get('/user/watchlist', 'UserController@getWatchlist');
    Route::get('/user/transactions', 'UserController@getTransactions');

    Route::get('/user/profile', 'UserController@profile');
    Route::post('/user/profile/update', 'UserController@update');
    Route::get('/user/portfolio', 'UserController@portfolio');
    Route::get('/user/getMaxBuy', 'UserController@getMaxBuy');
    Route::get('/user/getMaxSell', 'UserController@getMaxSell');

    Route::post('/user/profile/avatarUpdate', 'UserController@uploadProfile');

});

Route::group(['prefix' => 'api/v1', 'middleware' => 'cors'], function () {

    Route::get('/stock/data/{symbol}/{options?}', 'StockController@show');
    Route::get('/stock/history/{symbol}/{options?}', 'StockController@getHistory');
    Route::get('/stocks/top', 'StockController@top');
    Route::get('/stocks/recommendation', 'StockController@recommendation');
    Route::get('/stock/search', 'StockController@search');

    Route::post('/user/isUsernameAvailable', 'UserController@isUsernameAvailable');
    Route::post('/user/create', 'UserController@store');
    Route::post('/user/authenticate', 'UserController@authenticate');

});
