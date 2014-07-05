<?php

return [

	'default' => 'mysql',

	'connections' => [

		/*'sqlite' => [
			'driver'   => 'sqlite',
			'database' => __DIR__.'/../database/production.sqlite',
			'prefix'   => '',
		],*/

		'mysql' => [
			'driver'    => 'mysql',
			'host'      => '127.0.0.1',
			'database'  => 'test',
			'username'  => 'root',
			'password'  => 'ohkazer',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		],

		/*'pgsql' => [
			'driver'   => 'pgsql',
			'host'     => 'localhost',
			'database' => 'database',
			'username' => 'root',
			'password' => '',
			'charset'  => 'utf8',
			'prefix'   => '',
			'schema'   => 'public',
		],

		'sqlsrv' => [
			'driver'   => 'sqlsrv',
			'host'     => 'localhost',
			'database' => 'database',
			'username' => 'root',
			'password' => '',
			'prefix'   => '',
		],*/

	],
];