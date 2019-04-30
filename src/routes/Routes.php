<?php

Route::group(['namespace' => 'ongoingcloud\\laravelcrud\\Http\\Controllers', 'prefix' => 'autolaravel'], function () {
	// Route::group(['middleware' => ['web', 'auth', 'locale:en']], function () {
		Route::get('permissionmodules', 'PermissionmoduleController@index')->name('permissionmodule.index');
		Route::get('permissionmodules-paginate','PermissionmoduleController@Paginate')->name('permissionmodule.paginate');
		Route::get('permissionmodule/create', 'PermissionmoduleController@create')->name('permissionmodule.create');
		Route::post('permissionmodule/store', 'PermissionmoduleController@store')->name('permissionmodule.store');
		Route::get('permissionmodule/edit/{id}', 'PermissionmoduleController@edit')->name('permissionmodule.edit');
		Route::post('permissionmodule/destroy', 'PermissionmoduleController@destroy')->name('permissionmodule.destroy');
	// });
});