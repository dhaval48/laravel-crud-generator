

Route::group(['namespace' => 'Backend', 'prefix' => '[LAMODULE]'], function () {
	Route::group(['middleware' => ['auth', 'locale:en']], function () {
		Route::get('[MODULE]s', '[UMODULE]Controller@index')->name('[MODULE].index');
		Route::get('[MODULE]s-paginate','[UMODULE]Controller@Paginate')->name('[MODULE].paginate');
		Route::get('[MODULE]/create', '[UMODULE]Controller@create')->name('[MODULE].create');
		Route::post('[MODULE]/store', '[UMODULE]Controller@store')->name('[MODULE].store');
		Route::get('[MODULE]/edit/{id}', '[UMODULE]Controller@edit')->name('[MODULE].edit');
		Route::post('[MODULE]/destroy', '[UMODULE]Controller@destroy')->name('[MODULE].destroy');
	});
});