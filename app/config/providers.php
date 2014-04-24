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
	$driverName = Config::get('cache.driver');
	$className = "Framework\\Cache\\".ucfirst($driverName)."Cache";
	$driver = new $className;
	$cfgDrivers = Config::get("cache.drivers");
	$driver->setConfig($cfgDrivers[$driverName]);
	$cache = new Framework\Cache\Cache($driver);
	return $cache;
});

