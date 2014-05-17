<?php

return [
	'driver' => 'mongo',

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
		'mongo' => [

			'host' => '127.0.0.1',

			'port' => 27017,

			'username' => null,

			'password' => null,

			'database' => 'cachedb',
			'collection' => 'testcollection',

			'defaultCacheLifeTime'=>900, // lifetime unit is Second

		],

	],
];