<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::post('/ingredients', 'AlcoholController@getAllIngredients')->name('ingredients');
Route::get('/searchSuggestions', 'AlcoholController@forSearchSuggestions')->name('searchSuggestions');
Route::get('/getSessionCocktails', 'AlcoholController@getSessionCocktails')->name('getSessionCocktails');
Route::get('/getSessionNormalDrinks', 'AlcoholController@getSessionNormalDrinks')->name('getSessionNormalDrinks');
Route::get('/getDummyList', 'AlcoholController@dummyList')->name('getDummyList');
Route::get('/getDrink/{id}', 'AlcoholController@getSingularDrinkInfo')->name('getDrink');
Route::post('/getList', 'AlcoholController@getList')->name('getList');
Route::post('/getNormalList', 'AlcoholController@getNormalList')->name('getNormalList');
Route::post('/addDrink', 'AlcoholController@addDrink')->name('addDrink');
Route::post('/removeFromCartApi', 'AlcoholController@removeFromCartApi')->name('removeFromCartApi');
Route::post('/removeFromCartNormal', 'AlcoholController@removeFromCartNormal')->name('removeFromCartNormal');
Route::post('/emptyCart', 'AlcoholController@emptyCart')->name('emptyCart');
Route::post('/changeQuantity', 'AlcoholController@changeQuantityApi')->name('changeQuantity');
Route::post('/changeQuantityNormal', 'AlcoholController@changeQuantityNormal')->name('changeQuantityNormal');
Route::post('/changeABV', 'AlcoholController@changeABV')->name('changeABV');
Route::post('/calculate', 'AlcoholController@calculate')->name('calculate');
Route::get('/getNormalDrinks', 'AlcoholController@getNormalDrinks')->name('getNormalDrinks');
Route::post('/addOrdinaryDrink', 'AlcoholController@addOrdinaryDrink')->name('addOrdinaryDrink');
Route::post('/testingNormals', 'AlcoholController@testingNormals')->name('testingNormals');
Route::get('/prevSessions/{userId}', 'AlcoholController@retrievePreviousSessions')->name('prevSessions');
Route::post('/deleteSession', 'AlcoholController@deleteSession')->name('deleteSession');
Route::get('/favouriteDrink/{userId}', 'AlcoholController@favouriteDrink')->name('favouriteDrink');


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'Auth\AuthController@login')->name('login');
    Route::post('register', 'Auth\AuthController@register');
    Route::post('edit', 'Auth\AuthController@edit');
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::post('logout', 'Auth\AuthController@logout'); //beshe get method
        Route::get('user', 'Auth\AuthController@user');
    });
});
