<?php

use Framework\Support\Autoload;

require __DIR__ . '/../../vendor/Framework/Support/Autoload.php';

$paths = [
	__DIR__ . '/../' . 'controllers'
];

Autoload::addDirectories($paths);

Autoload::register();