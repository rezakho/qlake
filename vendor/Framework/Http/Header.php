<?php

namespace Framework\Http;

use Framework\Http\Environment;
use ArrayIterator;
use Countable;
use IteratorAggregate;

class Header
{
	/**
	 * Array of headers.
	 * 
	 * @var array
	 */
	protected $data = [];

	/**
	 * Create new instance Header.
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->data = $this->getAllHeaders();
	}

	/**
	 * Get all headers.
	 * 
	 * @return array
	 */
	protected function getAllHeaders()
	{
		return getallheaders();
	}

	/**
	 * Set a HTTP header.
	 * 
	 * @param string $name 
	 * @param string $value 
	 * @return Framework\Http\Header
	 */
	public function set($name, $value)
	{
		$this->data[$this->normalizeKey($name)] = $value;

		return $this;
	}

	/**
	 * Get a HTTP header by name.
	 * 
	 * @param string $name 
	 * @param string|null $default 
	 * @return string|null
	 */
	public function get($name, $default = null)
	{
		// must add closure support for $default later!
		return $this->data[$this->normalizeKey($name)] ?: (string)$default;
	}

	/**
	 * Check exist a header by name.
	 * 
	 * @param string $name 
	 * @return bool
	 */
	public function has($name)
	{
		return array_key_exists($this->normalizeKey($name), $this->data);
	}

	/**
	 * Get all HTTP headers.
	 * 
	 * @return array
	 */
	public function all()
	{
		return $this->data;
	}

	/**
	 * Clear all HTTP headers.
	 * 
	 * @return Framework\Http\Header
	 */
	public function clear()
	{
		$this->data = [];

		return $this;
	}

	/**
	 * Remove one HTTP header by name.
	 * 
	 * @param string $name 
	 * @return Framework\Http\Header
	 */
	public function remove($name)
	{
		unset($this->data[$this->normalizeKey($name)]);

		return $this;
	}

	/**
	 * Get all HTTP header names.
	 * 
	 * @return array
	 */
	public function keys()
	{
		return array_keys($this->data);
	}

	/**
	 * Normalize HTTP header name.
	 * 
	 * @param string $name 
	 * @return string
	 */
	protected function normalizeKey($name)
	{
		$name = strtolower($name);
		$name = str_replace(array('-', '_'), ' ', $name);
		$name = preg_replace('#^http #', '', $name);
		$name = ucwords($name);
		$name = str_replace(' ', '-', $name);

		return $name;
	}
}