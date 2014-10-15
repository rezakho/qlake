<?php
/*
Route::get('/', function()
{
	echo View::render('index');
});
*/
Route::get('/', function()
{

	//$db = DB::select('*')->from('users')
	//->join('posts')->on('users.id', '=', 'posts.userId')
	//->where('id', '>', 12)
	//;

	//trace($db->toSql());

	//trace(Html::label('label text', ['attr1' => 'value1', 'attr2' => 'valu""""\'"e2']));

	//$f = new Framework\Html\FormBuilder(new Framework\Html\HtmlBuilder, 'csrfToken');

	//echo $f->url('name', 'value', ['id'=>'id']);

	$dbh = new PDO('mysql:host=localhost;port=3306;dbname=test', 'root', 'ohkazer', array( PDO::ATTR_PERSISTENT => false));

    $stmt = $dbh->prepare("select * from users");

    //$stmt->bindValue(1, 'users');

    // call the stored procedure
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);

	trace($rows);
});










