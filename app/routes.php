<?php

Route::get('/', function()
{
	echo View::render('index');
});

Route::get('/', function()
{

	$db = DB::select('users.id as iid', 'count(*) as c')->from('users')->where('id', '=', '145')->or(function($query)
	{
		$query->where('id', '=', 3)->or('id', 'between', [10,15]);
	});

	trace($db->all());
});










