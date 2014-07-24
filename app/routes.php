<?php
/*
Route::get('/', function()
{
	echo View::render('index');
});
*/
Route::get('/', function()
{

	$db = DB::select('*')->from('users')->join(function($query){
		$query->select('*')->from('role');
	})->on('users.roleId', '=', 'role.id')->or('users.roleId', '=', 'role.id')->and(function($join){
		$join->on('users.roleId', '=', 'role.id')->and('users.roleId', '=', 'role.id');
	})->where('id', '>', 12);

	trace($db->toSql());

	//trace(Html::label('label text', ['attr1' => 'value1', 'attr2' => 'valu""""\'"e2']));

	//$f = new Framework\Html\FormBuilder(new Framework\Html\HtmlBuilder, 'csrfToken');

	//echo $f->url('name', 'value', ['id'=>'id']);
});










