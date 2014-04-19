<?php

namespace Framework\Architecture;

use Framework\Application;

trait Iwan
{
	/**
	 * Application instans that is running.
	 *
	 * @var Framework\Application
	 */
	protected static $app;

	/**
	 * Get instans of application service by name. The name specified in
	 * each Iwan class by static property named $provider.
	 *
	 * @return mixed Application service that is Object
	 */
	public final static function getInstans()
	{
		$app = static::$app;

		return $app[static::$provider];
	}

	/**
	 * Get application instans.
	 *
	 * @return Framework\Application
	 */
	public static function getApplication()
	{
		return static::$app;
	}

	/**
	 * Set application instans.
	 * 
	 * @param Framework\Application $app
	 * @return void
	 */
	public static function setApplication(Application $app)
	{
		static::$app = $app;
	}

	/**
	 * Magic method!
	 * This method provides static method interface for objective method of
	 * applicatin services. This is heart of Iwans;
	 */
	public final static function __callStatic($name, $arguments)
	{
		$instans = static::getInstans();

		return call_user_func_array(array($instans, $name), $arguments);
	}
}