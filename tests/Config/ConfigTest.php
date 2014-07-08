<?php

class ConfigTest extends PHPUnit_Framework_TestCase
{
	public function testSetNameKeyConfig()
	{
		$config = $this->getConfigInstance();

		$config->set('name.key','value');

		$value = $config->get('name.key');

		$this->assertEquals('value', $value);
	}

	public function testGetNameKeyConfig()
	{
		$config = $this->getConfigInstance();

		$value = $config->get('database.default');

		$this->assertEquals('mysql', $value);
	}

	public function testSetAllConfig()
	{
		$config = $this->getConfigInstance();

		$config->set('name', ['key' => 'value']);

		$value = $config->get('name');

		$this->assertEquals('value', $value['key']);
	}

	public function testGetAllConfig()
	{
		$config = $this->getConfigInstance();

		$value = $config->get('database');

		$this->assertEquals('mysql', $value['default']);
	}

	public function testSetAndGetAliases()
	{
		$config = $this->getConfigInstance();

		$value = $config->aliases(['alias' => 'c:/']);

		$aliases = $config->aliases();

		$this->assertEquals('c:/', $aliases['alias']);
	}

	public function testAddAlias()
	{
		$config = $this->getConfigInstance();

		$value = $config->alias('alias', 'c:/');

		$alias = $config->alias('alias');

		$this->assertEquals('c:/', $alias);
	}

	public function testSetAliasNameKeyConfig()
	{
		$config = $this->getConfigInstance();

		$config->set('alias::name.key', 'value');

		$value = $config->get('alias::name.key');

		$this->assertEquals('value', $value);
	}

	public function testGetAliasNameKeyConfig()
	{
		$config = $this->getConfigInstance();

		$config->alias('alias', $config->getLocalPath());

		$value = $config->get('alias::database.default');

		$this->assertEquals('mysql', $value);
	}

	public function getConfigInstance()
	{
		$localPath = __DIR__ . '/../../app/config';

		$config = new Framework\Config\Config();

		$config->setLocalPath($localPath);

		return $config;
	}
}