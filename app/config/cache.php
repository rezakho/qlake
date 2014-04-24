<?php

return [
	'driver' => 'file',

	'prefix' => '',

	'drivers' => [

		'file' => [

			'path' => __DIR__ . '/../storage/cache',

			'defaultCacheLifeTime'=>900, // lifetime unit is Second
			
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