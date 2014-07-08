<?php
/**
 * In this file register application`s dependencies.
 * For use of these, just use of $app['provider'].
 * These are lazyload. So high the performance.
 */

/**
 * Register router singleton provider.
 */
App::singleton('router', function($app)
{
	return new Framework\Routing\Router;
});

/**
 * Register request singleton provider.
 */
App::singleton('request', function($app)
{
	return new Framework\Http\Request;
});

/**
 * Register view singleton provider.
 */
App::bind('view', function($app)
{
	$view = new Framework\View\View;

	$view->setPaths([__DIR__ . '/../views']);

	return $view;
});

/**
 * Register config singleton provider.
 */
App::singleton('config', function($app)
{
	$localPath = __DIR__;

	$config = new Framework\Config\Config();

	$config->setLocalPath($localPath);

	return $config;
});

/**
 * Register database PDo connection singleton provider.
 */
App::bind('db', function($app)
{
	$cf = new Framework\Database\Connection\ConnectionFactory(Config::get('database'));

	$connector = $cf->createConnector();

	$connection = $connector->createConnection();

	return new Framework\Database\Query($connection, new Framework\Database\Grammar\MysqlGrammar);
});


/**
 * Register cache singleton provider.
 */
App::singleton('cache', function($app)
{
	$driverName = Config::get('cache.driver');
	$className = "Framework\\Cache\\".ucfirst($driverName)."Cache";
	$driver = new $className;
	$cfgDrivers = Config::get("cache.drivers");
	$driver->setConfig($cfgDrivers[$driverName]);
	$cache = new Framework\Cache\Cache($driver);
	return $cache;
});

