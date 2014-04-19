<?php

namespace Framework\Http;

use Framework\Http\Header;

class Request
{

	protected $header;

	protected $method;

	protected $query = [];

	protected $data = [];

	public $env;

	protected $cookie;

	protected $files;

	public function __construct($queryString = null, $postData = null, $server = null, $cookies = null, $files = null, $content = null)
	{
		$this->header = new Header;
		$this->env = new Environment;;
		$this->query  = $this->parseInputs($_GET);
		$this->data   = $this->parseInputs($_POST);
	}

	public function parseInputs(array $inputs)
	{
		$specials = [
			'_method',
			'_csrf',
		];

		$parsedInputs = [];

		foreach ($inputs as $key => $value)
		{
			$newKey = array_key_exists(strtolower($key), $specials) ? strtolower($key) : $key;

			$parsedInputs[$newKey] = $value;
		}

		return $parsedInputs;
	}

	protected function detectMethod()
	{
		$method = $this->env['REQUEST_METHOD'];
		
		return strtoupper($this->getData('_method', $method));
	}

	protected function getMethod()
	{
		return $this->method = $this->method ?: $this->detectMethod();
	}

	public function method()
	{
		return $this->getMethod();
	}

	protected function getHeader()
	{
		return $this->header;
	}

	public function header()
	{
		return $this->getHeader();
	}

	public function getQuery($name, $default = null)
	{
		return $this->query[$name] ?: $default;
	}

	public function getData($name, $default = null)
	{
		return $this->data[$name] ?: $default;
	}

	public function input($name, $default = null)
	{
		return $this->query[$name] ?: $this->data[$name] ?: $default;
	}

}