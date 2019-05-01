
Route::group(['namespace' => 'API'], function () {
	Route::group(['middleware' => ['auth:api', 'client']], function () {
		Route::get('[MODULE]s', '[UMODULE]Controller@index')->name('[MODULE].index');
		Route::post('[MODULE]/store', '[UMODULE]Controller@store')->name('[MODULE].store');
		Route::post('[MODULE]/update', '[UMODULE]Controller@store')->name('[MODULE].update');
		Route::get('[MODULE]/edit/{id}', '[UMODULE]Controller@edit')->name('[MODULE].edit');
		Route::get('[MODULE]/destroy/{id}', '[UMODULE]Controller@destroy')->name('[MODULE].destroy');
	});
});