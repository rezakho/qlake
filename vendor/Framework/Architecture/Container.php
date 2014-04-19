<?php

namespace Framework\Architecture;

use ArrayAccess;
use Framework\Exception\ClearException;

class Container implements ArrayAccess
{
	/**
     * Set of application providers.
     *
     * @var Framework\Application
     */
	protected $providers = [];
	
	/**
     * Register.
     *
     * @param Framework\Application
     * @return void
     */
	public function singleton($name, $provider)
	{
		if ($this->providers[$name])
		{
			throw new ClearException("Service '$name' is already set.", 2);
		}

		$this->offsetSet($name, $provider);
	}



	public function offsetSet($name, $provider)
	{
		$this->providers[$name] = $provider;
	}



	public function offsetGet($name)
	{
		if (isset($this->providers[$name]))
		{
			if (get_class($this->providers[$name]) == 'Closure')
			{
				$this->providers[$name] = $this->providers[$name]();

				return $this->providers[$name];
			}
			
			return $this->providers[$name];
		}

		throw new \InvalidArgumentException("Service $name not found!", 1);
	}



	public function offsetExists($name)
	{
		return isset($this->providers[$name]);
	}



	public function offsetUnset($name)
	{
		unset($this->providers[$name]);
	}
}

