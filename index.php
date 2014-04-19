<?php


error_reporting(E_ALL & ~E_NOTICE);


require __DIR__ . '/app/start/autoload.php';


$app = require __DIR__ . '/app/start/start.php';


$app->run();

