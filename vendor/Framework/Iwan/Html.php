<?php

namespace Framework\Iwan;

use Framework\Architecture\Iwan;

abstract class Html
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
	private static $provider = 'html';
}