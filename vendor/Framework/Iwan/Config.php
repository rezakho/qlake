<?php

namespace Framework\Iwan;

use Framework\Architecture\Iwan;

abstract class Config
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
	private static $provider = 'config';
}