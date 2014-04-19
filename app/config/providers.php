<?php
/**
 * In this file register application`s dependencies.
 * For use of these, just use of $app['provider'].
 * These are lazyload. So high the performance.
 */

/**
 * Register router singleton provider.
 */
App::singleton('router', function()
{
	return new Framework\Routing\Router;
});

/**
 * Register request singleton provider.
 */
App::singleton('request', function()
{
	return new Framework\Http\Request;
});

/**
 * Register view singleton provider.
 */
App::singleton('view', function()
{
	$view = new Framework\View\View;

	$view->setPaths([__DIR__ . '/../views']);

	return $view;
});

/**
 * Register config singleton provider.
 */
App::singleton('config', function()
{
	$localPath = __DIR__;

	$config = new Framework\Config\Config();

	$config->setLocalPath($localPath);

	return $config;
});

/**
 * Register cache singleton provider.
 */
App::singleton('cache', function()
{
	/*$driverClass = ucwords(strtolower(Config::get('cache.driver')));

	$prefix = Config::get('cache.prefix');

	$drivers = Config::get('cache.drivers');

	$cache = new Framework\Cache\Cache();

	$driver = new $driverClass($drivers[]);

	$cache->setDriver();

	$config->setLocalPath($localPath);

	return $config;*/
});

