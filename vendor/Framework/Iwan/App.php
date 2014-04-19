<?php

namespace Framework\Iwan;

use Framework\Architecture\Iwan;

abstract class App
{
	/**
	 * Use Iwan trait for this class
	 */
	use Iwan;

	/**
	 * Determine the application service name
	 * 
	 * @var string
	 */
	private static $provider = 'app';
}