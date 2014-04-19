<?php

namespace Framework\Cache;

interface CacheDriverInterface
{
	/**
	 * Set new value to cache.
	 * 
	 * @param string $key 
	 * @param mixed $value 
	 * @param int $expirtion second
	 * @return bool
	 */
	public function set($key, $value, $expirtion);

	/**
	 * Get the cached value.
	 * @param string $key 
	 * @param mixed $default 
	 * @return mixed|null
	 */
	public function get($key, $default = null);

	/**
	 * Check exist the key in ckached data.
	 * @param string $key 
	 * @return bool
	 */
	public function has($key);

	/**
	 * Get value if it exists or Set and Get value if it not exists.
	 *   
	 * @param string $key 
	 * @param mixed $value 
	 * @param int $expirtion 
	 * @return mixed return value
	 */
	public function remember($key, $value, $expirtion);

	/**
	 * Remove data from cache.
	 * 
	 * @param string $key 
	 * @return bool
	 */
	public function remove($key);
}