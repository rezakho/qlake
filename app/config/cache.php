<?php

return [
	'driver' => 'file',

	'prefix' => '',

	'drivers' => [

		'file' => [

			'path' => _DIR_ . '/../storage/cache',

		],

		'database' => [

			'connection' => null,

			'table' => 'cache',

		],

		'memcached' => [

			['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100],

		],

	],
];