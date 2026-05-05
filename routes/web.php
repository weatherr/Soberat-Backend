<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/config-cache', function() {
    Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

Route::get('/alcohol', 'AlcoholController@index')->name('alcohol');
Route::post('/adddrink', 'AlcoholController@calculateDrinks')->name('calculate_drinks');
Route::get('/removeFromCart/{drink}', 'AlcoholController@removeFromCart')->name('removeFromCart'); //pitai marto dali ne trqbva da e post? shtoto activate_code e post
Route::get('/emptyCartApi', 'AlcoholController@emptyCartApi')->name('emptyCartApi');
Route::get('/changeQuantity/{drink}', 'AlcoholController@changeQuantity')->name('changeQuantity');
Route::get('/uberpage', 'AlcoholController@uberPage')->name('uberPage');
Route::get('/logout', 'AlcoholController@logout')->name('logout');
Route::get('/preview', 'AlcoholController@getPreviousSessions')->name('preview');
// Route::get('/save_user_info', 'AlcoholController@save_user_info')->name('save_user_info');
Route::get('/calculate', 'AlcoholController@calculate')->name('calculate');
Route::get('/getList', 'AlcoholController@getList')->name('getList');


//userInfo
Route::post('/save_user_info', 'HomeController@save_user_info')->name('save_user_info');



Route::get('/', function () {
    // return view('welcome');
    return route('alcohol');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


// Route::get('/searchSuggestions', 'AlcoholController@forSearchSuggestions')->name('searchSuggestions');
// Route::get('/ingredients', 'AlcoholController@getAllIngredients')->name('ingredients');
