<?php

namespace Framework\Database\Connector;

use PDO;
use PDOException;
use Framework\Exception\ClearException;
use Framework\Database\Connection\MysqlConnection;

class MysqlConnector extends Connector
{

	public function connect()
	{
		$config = $this->config;

		$pdo = $this->createPDO();

		$pdo->exec("USE {$config['database']}");

		$charset = $config['charset'];
		$collation = $config['collation'];

		$names = "SET NAMES '$charset'" . $collation ? " collate '$collation'" : '';

		$pdo->prepare($names)->execute();

		return new MysqlConnection($pdo, $this->config);
	}

	public function createConnection()
	{
		return $this->connect();
	}

	public function createPDO()
	{
		$dsn = $this->createDSN();

		try
		{
			return new PDO($dsn, $this->config['username'], $this->config['password']);
		}
		catch (PDOException $e)
		{
			throw new ClearException($e->getMessage(), 0);
		}
	}

	public function createDSN()
	{
		$config = $this->config;

		return "{$config['driver']}:host={$config['host']};dbname={$config['database']}";
	}

	
}