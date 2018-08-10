<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');


//Route::get('home', 'homeController@index');
Route::group(['prefix'=>'k','namespace'=>'k'],function(){

	Route::get('/', 'KController@index');
	Route::resource('km', 'KmController');
	Route::resource('kr', 'KrController');
});



Route::group(['prefix'=>'z','namespace'=>'z'],function(){

	Route::get('/', 'ZController@index');
	Route::resource('zb', 'ZbController');
	Route::resource('zr', 'ZrController');
});

Route::group(['prefix'=>'p','namespace'=>'p'],function(){

	Route::get('/', 'PController@index');
	Route::resource('reset', 'ResetController');
});

Route::group(['prefix'=>'s','namespace'=>'s'],function(){

	Route::get('/', 'SController@index');
	Route::resource('sk', 'SkController');
	Route::resource('sh', 'ShController');
	Route::resource('sz', 'SzController');
	Route::resource('hg', 'HgController');
	Route::resource('hk', 'HkController');
	Route::resource('hl', 'HlController');
});

Route::group(['prefix'=>'j','namespace'=>'j'],function(){

	Route::get('/', 'KaController@index');
	Route::resource('ka', 'KaController');
	Route::resource('bu', 'BuController');
	Route::resource('bu1', 'Bu1Controller');
	Route::resource('me', 'MeController');
	Route::resource('me1', 'Me1Controller');
	Route::resource('me2', 'Me2Controller');
	Route::resource('me3', 'Me3Controller');
	Route::resource('me4', 'Me4Controller');
	Route::resource('me5', 'Me5Controller');

});

Route::controllers([
		'auth' => 'Auth\AuthController',
		'password' => 'Auth\PasswordController'
]);

Route::group(['prefix'=>'h','namespace'=>'h'],function(){


	Route::get('/', 'hController@index');
	Route::resource('hk', 'HkController');
	Route::resource('hg', 'HgController');
	Route::resource('hl', 'HlController');
	Route::resource('hr', 'HrController');
});



