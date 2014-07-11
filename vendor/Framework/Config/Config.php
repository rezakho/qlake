<?php

namespace Framework\Config;

use Framework\Exception\ClearException;

class Config
{
	/**
	 * Array of config aliases
	 * 
	 * @var array
	 */
	protected $aliases = [];

	/**
	 * Array of configs
	 * 
	 * @var array
	 */
	protected $configs = [];

	/**
	 * Determine alias separator string
	 * 
	 * @var string
	 */
	protected $aliasSeparator = '::';

	/**
	 * Determine name and key separator string
	 * 
	 * @var string
	 */
	protected $nameSeparator = '.';

	/**
	 * Constructor method
	 * 
	 * @return null
	 */
	public function __construct()
	{}

	/**
	 * Set or get config aliases
	 * 
	 * @param array $aliases 
	 * @return array|null
	 */
	public function aliases(array $aliases = null)
	{
		if (is_null($aliases))
		{
			return $this->aliases;
		}
		else
		{
			$this->aliases = array_merge($this->aliases, $aliases);
		}
	}

	/**
	 * Set or get a config alias
	 * 
	 * @param string $alias 
	 * @param string $path 
	 * @return string|null
	 */
	public function alias($alias, $path = null)
	{
		if (is_null($path))
		{
			return $this->aliases[$alias] ?: null;
		}
		else
		{
			$this->aliases[$alias] = $path;
		}
	}

	/**
	 * Set default application config path
	 * 
	 * @param string $path 
	 * @return null
	 */
	public function setDefaultPath($path)
	{
		$this->aliases['default'] = rtrim($path, '/') . DIRECTORY_SEPARATOR;
	}

	/**
	 * Get default application config path
	 * 
	 * @return string
	 */
	public function getDefaultPath()
	{
		return $this->aliases['default'];
	}

	/**
	 * Open and load configs from a file
	 * 
	 * @param string $alias
	 * @param string $configName
	 * @return array
	 */
	protected function loadConfig($alias, $configName)
	{
		if (isset($this->aliases[$alias]))
		{
			$aliasPath = $this->aliases[$alias];
		}
		else
		{
			throw new ClearException("Config alias '$alias' not found", 4);
		}

		$file = $aliasPath . $configName . '.php';

		if (is_file($file))
		{
			return $this->configs[$alias][$configName] = require $file;
		}
		
		throw new ClearException("The config file '$file' not found!", 4);
	}

	/**
	 * Get a config
	 * 
	 * @param string $mixedKey May be a key or a config file name
	 * @param mixed $default 
	 * @return array|mixed
	 */
	public function get($mixedKey, $default = null)
	{
		list($alias, $configName, $key) = $this->parseKey($mixedKey);

		if (is_null($key))
		{
			return $this->configs[$alias][$configName] ?: $this->loadConfig($alias, $configName) ?: $default;
		}

		return $this->configs[$alias][$configName][$key] ?: $this->loadConfig($alias, $configName)[$key] ?: $default;
	}

	/**
	 * Set a config
	 * 
	 * @param string $mixedKey May be a key or a config file name
	 * @param mixed $value 
	 * @return array|mixed
	 */
	public function set($mixedKey, $value)
	{
		list($alias, $configName, $key) = $this->parseKey($mixedKey);

		if (is_null($key))
		{
			$this->configs[$alias][$configName] = $value;
		}

		$this->configs[$alias][$configName][$key] = $value;
	}

	/**
	 * Parse requested mixed-key and split alias, name and key
	 * 
	 * @param string $mixedKey 
	 * @return array An array like [alias, name, key]
	 */
	protected function parseKey($mixedKey)
	{
		if (preg_match('/^((?P<alias>[\w]+)::)?(?P<name>\w+)(\.(?P<key>\w+))?$/', $mixedKey, $matches) !==1)
		{
			throw new ClearException("Invalid format. Config name must be like 'alias::name.key' or 'name.key'.", 4);
		}

		$alias = $matches['alias'];
		$name  = $matches['name'];
		$key   = $matches['key'] ?: null;

		return [$alias ?: 'default', $name, $key];
	}
}