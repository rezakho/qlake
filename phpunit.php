<?php

require __DIR__ . '/app/start/autoload.php';

date_default_timezone_set('UTC');

$db = new DB;

		$sql = $db->select('*')->from('table')->toSql();

		echo $sql;