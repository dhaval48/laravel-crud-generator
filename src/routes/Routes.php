<?php

Route::group(['namespace' => 'Ongoingcloud\\Laravelcrud\\Http\\Controllers'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		require (__DIR__ . '/Common.php');
	});
});

Route::group(['namespace' => 'Ongoingcloud\\Laravelcrud\\Http\\Controllers', 'prefix' => 'control'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('users', 'UserController@index')->name('user.index');
		Route::get('users-paginate','UserController@Paginate')->name('user.paginate');
		Route::get('user/create', 'UserController@create')->name('user.create');
		Route::post('user/store', 'UserController@store')->name('user.store');
		Route::get('user/edit/{id}', 'UserController@edit')->name('user.edit');
		Route::post('user/destroy', 'UserController@destroy')->name('user.destroy');

		Route::get('user/changepassword', 'UserController@createChangePassword')->name('changepassword.create');
		Route::post('user/changepassword', 'UserController@postChangePassword')->name('changepassword.store');
	});
});

Route::group(['namespace' => 'Ongoingcloud\\Laravelcrud\\Http\\Controllers', 'prefix' => 'control'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('roles', 'RoleController@index')->name('role.index');
		Route::get('roles-paginate','RoleController@Paginate')->name('role.paginate');
		Route::get('role/create', 'RoleController@create')->name('role.create');
		Route::post('role/store', 'RoleController@store')->name('role.store');
		Route::get('role/edit/{id}', 'RoleController@edit')->name('role.edit');
		Route::post('role/destroy', 'RoleController@destroy')->name('role.destroy');
	});
});

Route::group(['namespace' => 'Ongoingcloud\\Laravelcrud\\Http\\Controllers', 'prefix' => 'autolaravel'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('permissionmodules', 'PermissionmoduleController@index')->name('permissionmodule.index');
		Route::get('permissionmodules-paginate','PermissionmoduleController@Paginate')->name('permissionmodule.paginate');
		Route::get('permissionmodule/create', 'PermissionmoduleController@create')->name('permissionmodule.create');
		Route::post('permissionmodule/store', 'PermissionmoduleController@store')->name('permissionmodule.store');
		Route::get('permissionmodule/edit/{id}', 'PermissionmoduleController@edit')->name('permissionmodule.edit');
		Route::post('permissionmodule/destroy', 'PermissionmoduleController@destroy')->name('permissionmodule.destroy');
	});
});

Route::group(['namespace' => 'Ongoingcloud\\Laravelcrud\\Http\\Controllers', 'prefix' => 'autolaravel'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('formmodules', 'FormmoduleController@index')->name('formmodule.index');
		Route::get('formmodules-paginate','FormmoduleController@Paginate')->name('formmodule.paginate');
		Route::get('formmodule/create', 'FormmoduleController@create')->name('formmodule.create');
		Route::post('formmodule/store', 'FormmoduleController@store')->name('formmodule.store');
		Route::get('formmodule/edit/{id}', 'FormmoduleController@edit')->name('formmodule.edit');
		Route::post('formmodule/destroy', 'FormmoduleController@destroy')->name('formmodule.destroy');
	});
});

Route::group(['namespace' => 'Ongoingcloud\\Laravelcrud\\Http\\Controllers', 'prefix' => 'autolaravel'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('gridmodules', 'GridmoduleController@index')->name('gridmodule.index');
		Route::get('gridmodules-paginate','GridmoduleController@Paginate')->name('gridmodule.paginate');
		Route::get('gridmodule/create', 'GridmoduleController@create')->name('gridmodule.create');
		Route::post('gridmodule/store', 'GridmoduleController@store')->name('gridmodule.store');
		Route::get('gridmodule/edit/{id}', 'GridmoduleController@edit')->name('gridmodule.edit');
		Route::post('gridmodule/destroy', 'GridmoduleController@destroy')->name('gridmodule.destroy');
	});
});

Route::group(['namespace' => 'Ongoingcloud\\Laravelcrud\\Http\\Controllers', 'prefix' => 'autolaravel'], function () {
	Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('apimodules', 'ApimoduleController@index')->name('apimodule.index');
		Route::get('apimodules-paginate','ApimoduleController@Paginate')->name('apimodule.paginate');
		Route::get('apimodule/create', 'ApimoduleController@create')->name('apimodule.create');
		Route::post('apimodule/store', 'ApimoduleController@store')->name('apimodule.store');
		Route::get('apimodule/edit/{id}', 'ApimoduleController@edit')->name('apimodule.edit');
		Route::post('apimodule/destroy', 'ApimoduleController@destroy')->name('apimodule.destroy');
	});
});

Route::group(['namespace' => 'Ongoingcloud\\Laravelcrud\\Http\\Controllers', 'prefix' => 'general'], function () {
	Route::group(['middleware' => ['web', 'auth']], function () {
		Route::get('languagetranslets', 'LanguagetransletController@index')->name('languagetranslet.index');
		Route::get('languagetranslets-paginate','LanguagetransletController@Paginate')->name('languagetranslet.paginate');
		Route::get('langarray-pagination','LanguagetransletController@getLangArrayPagination')->name('get.lang_array_pagination');
		Route::get('languagetranslet/create', 'LanguagetransletController@create')->name('languagetranslet.create');
		Route::post('languagetranslet/store', 'LanguagetransletController@store')->name('languagetranslet.store');
		Route::get('languagetranslet/edit/{id}', 'LanguagetransletController@edit')->name('languagetranslet.edit');
		Route::post('languagetranslet/destroy', 'LanguagetransletController@destroy')->name('languagetranslet.destroy');
	});
});