<?php

Route::group(['namespace' => 'ongoingcloud\\laravelcrud\\Http\\Controllers'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		require (__DIR__ . '/Common.php');
	});
});

Route::group(['namespace' => 'ongoingcloud\\laravelcrud\\Http\\Controllers', 'prefix' => 'autolaravel'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('permissionmodules', 'PermissionmoduleController@index')->name('permissionmodule.index');
		Route::get('permissionmodules-paginate','PermissionmoduleController@Paginate')->name('permissionmodule.paginate');
		Route::get('permissionmodule/create', 'PermissionmoduleController@create')->name('permissionmodule.create');
		Route::post('permissionmodule/store', 'PermissionmoduleController@store')->name('permissionmodule.store');
		Route::get('permissionmodule/edit/{id}', 'PermissionmoduleController@edit')->name('permissionmodule.edit');
		Route::post('permissionmodule/destroy', 'PermissionmoduleController@destroy')->name('permissionmodule.destroy');
	});
});

Route::group(['namespace' => 'ongoingcloud\\laravelcrud\\Http\\Controllers', 'prefix' => 'autolaravel'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('formmodules', 'FormmoduleController@index')->name('formmodule.index');
		Route::get('formmodules-paginate','FormmoduleController@Paginate')->name('formmodule.paginate');
		Route::get('formmodule/create', 'FormmoduleController@create')->name('formmodule.create');
		Route::post('formmodule/store', 'FormmoduleController@store')->name('formmodule.store');
		Route::get('formmodule/edit/{id}', 'FormmoduleController@edit')->name('formmodule.edit');
		Route::post('formmodule/destroy', 'FormmoduleController@destroy')->name('formmodule.destroy');
	});
});

Route::group(['namespace' => 'ongoingcloud\\laravelcrud\\Http\\Controllers', 'prefix' => 'autolaravel'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('gridmodules', 'GridmoduleController@index')->name('gridmodule.index');
		Route::get('gridmodules-paginate','GridmoduleController@Paginate')->name('gridmodule.paginate');
		Route::get('gridmodule/create', 'GridmoduleController@create')->name('gridmodule.create');
		Route::post('gridmodule/store', 'GridmoduleController@store')->name('gridmodule.store');
		Route::get('gridmodule/edit/{id}', 'GridmoduleController@edit')->name('gridmodule.edit');
		Route::post('gridmodule/destroy', 'GridmoduleController@destroy')->name('gridmodule.destroy');
	});
});

Route::group(['namespace' => 'ongoingcloud\\laravelcrud\\Http\\Controllers', 'prefix' => 'autolaravel'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('apimodules', 'ApimoduleController@index')->name('apimodule.index');
		Route::get('apimodules-paginate','ApimoduleController@Paginate')->name('apimodule.paginate');
		Route::get('apimodule/create', 'ApimoduleController@create')->name('apimodule.create');
		Route::post('apimodule/store', 'ApimoduleController@store')->name('apimodule.store');
		Route::get('apimodule/edit/{id}', 'ApimoduleController@edit')->name('apimodule.edit');
		Route::post('apimodule/destroy', 'ApimoduleController@destroy')->name('apimodule.destroy');
	});
});