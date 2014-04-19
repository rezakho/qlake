<?php

namespace Framework\Cache;

class Cache
{
	protected $driver;

	public function __construct(CacheDriverInterface $driver)
	{
		$this->driver = $driver;
	}
}