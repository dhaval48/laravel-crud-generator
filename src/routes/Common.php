<?php

Route::get('get-activity', 'CommonController@getActivity')->name('get.activity');
Route::get('get-file', 'CommonController@getFile')->name('get.file');

// Route::get('file', 'CommonController@fileCreate');
Route::post('upload/put', 'CommonController@fileUpload');
Route::get('/file/delete', 'CommonController@fileDelete');
Route::get('/file/download', 'CommonController@fileDownload');

Route::get('get-role', 'CommonController@getRoles')->name('get.role');

Route::get('get-lang-update/{locale}', 'CommonController@userLangUpdate')->name('get.lang_update');

Route::get('get-parent_form', 'CommonController@getParent_form')->name('get.parent_form');
Route::get('get-table', 'CommonController@getTable')->name('get.table');
Route::get('get-table_data', 'CommonController@getTable_data')->name('get.table_data');
Route::get('get-parent_module', 'CommonController@getParent_module')->name('get.parent_module');

Route::get('get-parent_api_form', 'CommonController@getParent_api_form')->name('get.parent_api_form');

Route::get('get-parent_api_table', 'CommonController@getParent_api_table')->name('get.parent_api_table');
// [CommonRoute]