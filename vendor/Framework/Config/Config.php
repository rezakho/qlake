<?php

namespace Framework\Config;

use Framework\Exception\ClearException;

class Config
{
	protected $aliases = [];

	protected $configs = [];

	protected $aliasSeparator = '::';

	protected $nameSeparator = '.';


	public function __construct()
	{
		
	}


	public function setLocalPath($path)
	{
		$this->aliases['local'] = rtrim($path, '/') . DIRECTORY_SEPARATOR;
	}


	protected function loadConfig($alias, $configName)
	{
		$file = $this->parseAlias($alias) . $configName . '.php';

		if (is_file($file))
		{
			return $this->configs[$alias][$configName] = require $file;
		}
		
		throw new ClearException("The config file '$file' not found!", 4);
	}


	public function get($mixedKey, $default = null)
	{
		list($alias, $configName, $key) = $this->parseKey($mixedKey);

		return $this->configs[$alias][$configName][$key] ?: $this->loadConfig($alias, $configName)[$key] ?: $default;
	}


	public function set($mixedKey, $value)
	{
		list($alias, $configName, $key) = $this->parseKey($mixedKey);

		$this->configs[$alias][$configName][$key] = $value;
	}


	protected function parseKey($mixedKey)
	{
		if (preg_match('/^((?P<alias>[\w]+)::)?(?P<name>\w+)\.(?P<key>\w+)$/', $mixedKey, $matches) !==1)
		{
			throw new ClearException("Invalid format. Config name must be like 'alias::name.key' or 'name.key'.", 4);
		}

		$alias = $matches['alias'];
		$name  = $matches['name'];
		$key   = $matches['key'];

		return [$alias ?: 'local', $name, $key];
	}


	protected function parseAlias($alias)
	{
		if (isset($this->aliases[$alias]))
		{
			return $this->aliases[$alias];
		}
		
		throw new ClearException("Config alias '$alias' not found", 4);
	}
}