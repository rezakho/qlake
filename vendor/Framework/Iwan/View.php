<?php

namespace Framework\Iwan;

use Framework\Architecture\Iwan;

abstract class View
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
	private static $provider = 'view';
}