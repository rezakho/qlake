<?php

namespace Framework\Exception;

use Exception;

class ClearException extends Exception
{
	public $function;

	public $class;

	public $type;

	public $args = [];

	public function __construct($message = "", $stackIndex = null)
	{
		$this->message = $message;

		if (!is_null($stackIndex))
		{
			$point = $this->getTrace()[(int)$stackIndex];

			$this->line     = $point['line'];

			$this->file     = $point['file'];

			$this->function = $point['function'];

			$this->class    = $point['class'];

			$this->type     = $point['type'];

			$this->args     = $point['args'];
		}
	}
}