<?php

namespace Framework\Database\Connector;

abstract class Connector
{
	public function __construct($config)
	{
		$this->config = $config;
	}

	abstract public function connect();

	abstract public function createPDO();

	abstract public function createDSN();
}