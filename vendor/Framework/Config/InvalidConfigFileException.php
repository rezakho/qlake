<?php

namespace Framework\Config;

use Exception;

class InvalidConfigFileException extends Exception
{

	public function __construct($message = "", $code = 0, Exception $previous = null)
	{
		$this->message = $message;

		$this->code = $code;

		$stack = $this->getTrace();

		$this->line = $stack[2]['line'];

		$this->file = $stack[2]['file'];
	}


	public function getPartCode()
	{
		
	}
}