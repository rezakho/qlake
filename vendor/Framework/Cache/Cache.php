<?php

namespace Framework\Cache;

class Cache
{
	protected $driver;

	public function __construct(CacheDriverInterface $driver)
	{
		$this->driver = $driver;
	}
	public function set($key, $value, $expiration = null)
	{
		return $this->driver->set($key, $value, $expiration);
	}

	public function get($key, $default = null){
		return $this->driver->get($key, $default);
	}

	public function has($key)
	{
		return $this->driver->has($key);
	}

	public function remember($key, $value, $expiration = null)
	{
		return $this->driver->remember($key, $value, $expiration);
	}

	public function remove($key)
	{
		$this->driver->remove($key);
	}
	
}