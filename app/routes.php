<?php
/*
Route::group('/', function()
{
	Route::group('test/hkh/gf', function()
	{
		Route::get('get.html', function(){
			echo '
		<form method="post" action="/framework/post">
		<input name="_method" value="PU"/>
		<input name="user"/>
		<input type="submit" />
		</form>
			';
		});
	});

	Route::get('post/{id:.+}', function($lang){
		echo 'route works!'.$lang;
		
	});
});
*/
Route::get('action', 'HomeController')->name('profile.index');
Route::get('سلام', 'HomeController');

Route::get('test', function(){
	if(Cache::has('var')){
		echo 'Cache Exists<br>';
		print_r(Cache::get('var'));
	}else{
		echo 'Cache Not Exists!<br>';
		Cache::set('var',array('name'=>'ali'),20);
	}
});

Route::get('/', function()
{
	$items = DB::select('*')->from('users')->get();

	trace($items);


	//Cache::get('var',array('name'=>'ali'),20);
});










